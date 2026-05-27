<?php
require_once __DIR__ . '/../../config/database.php';

class MdOrdenes
{
    public static function obtenerVehiculo($empresa_id, $vehiculo_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT 
                v.id AS vehiculo_id,
                v.placa,
                v.marca,
                v.modelo,
                v.anio,
                v.color,
                v.kilometraje,
                c.id AS cliente_id,
                c.nombre AS cliente,
                c.telefono,
                c.direccion
            FROM vehiculos v
            INNER JOIN clientes c ON c.id = v.cliente_id
            WHERE v.empresa_id = :empresa_id
            AND v.id = :vehiculo_id
            LIMIT 1
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':vehiculo_id', $vehiculo_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function listarProductos($empresa_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT 
                id,
                nombre,
                categoria,
                unidad,
                stock_actual,
                costo,
                precio_venta
            FROM productos
            WHERE empresa_id = :empresa_id
            AND estado = 'ACTIVO'
            ORDER BY categoria ASC, nombre ASC
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function guardarOrdenDirecta($empresa_id, $vehiculo, $datos, $items)
    {
        $database = new Database();
        $db = $database->connect();

        try {
            $db->beginTransaction();

            $total = 0;
            $itemsLimpios = [];

            foreach ($items as $item) {
                $tipo = strtoupper(trim($item['tipo_item'] ?? ''));
                $producto_id = !empty($item['producto_id']) ? (int)$item['producto_id'] : null;
                $descripcion = trim($item['descripcion'] ?? '');
                $cantidad = (float)($item['cantidad'] ?? 1);
                $costo = (float)($item['costo_unitario'] ?? 0);
                $precio = (float)($item['precio_unitario'] ?? 0);

                if ($cantidad <= 0) {
                    $cantidad = 1;
                }

                if (!in_array($tipo, ['SERVICIO', 'STOCK', 'EXTERNO'])) {
                    continue;
                }

                if ($tipo === 'STOCK') {
                    if (!$producto_id) {
                        throw new Exception('Selecciona un producto de stock.');
                    }

                    $stmtProducto = $db->prepare("
                        SELECT id, nombre, stock_actual, costo, precio_venta
                        FROM productos
                        WHERE id = :producto_id
                        AND empresa_id = :empresa_id
                        LIMIT 1
                        FOR UPDATE
                    ");

                    $stmtProducto->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
                    $stmtProducto->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
                    $stmtProducto->execute();

                    $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

                    if (!$producto) {
                        throw new Exception('Producto no encontrado.');
                    }

                    if ((float)$producto['stock_actual'] < $cantidad) {
                        throw new Exception(
                            'Stock insuficiente para ' . $producto['nombre'] .
                            '. Disponible: ' . $producto['stock_actual'] .
                            ', requerido: ' . $cantidad
                        );
                    }

                    $descripcion = $producto['nombre'];
                    $costo = (float)$producto['costo'];
                    $precio = (float)$producto['precio_venta'];
                }

                if ($descripcion === '') {
                    continue;
                }

                $subtotal = $cantidad * $precio;
                $total += $subtotal;

                $itemsLimpios[] = [
                    'tipo_item' => $tipo,
                    'producto_id' => $producto_id,
                    'descripcion' => $descripcion,
                    'cantidad' => $cantidad,
                    'costo_unitario' => $costo,
                    'precio_unitario' => $precio,
                    'subtotal' => $subtotal
                ];
            }

            if (count($itemsLimpios) === 0) {
                throw new Exception('Agrega al menos un item a la orden.');
            }

            $pago_estado = strtoupper(trim($datos['pago_estado'] ?? 'PAGADO'));
            $adelanto = (float)($datos['adelanto'] ?? 0);

            if (!in_array($pago_estado, ['PAGADO', 'FIADO', 'ADELANTO'])) {
                $pago_estado = 'PAGADO';
            }

            if ($pago_estado === 'PAGADO') {
                $adelanto = $total;
                $saldo = 0;
            } elseif ($pago_estado === 'FIADO') {
                $adelanto = 0;
                $saldo = $total;
            } else {
                if ($adelanto < 0) {
                    $adelanto = 0;
                }

                if ($adelanto > $total) {
                    $adelanto = $total;
                }

                $saldo = $total - $adelanto;
            }

            $codigo = 'ORD-' . date('YmdHis');
            $descripcionOrden = trim($datos['descripcion'] ?? 'Orden directa');

            $stmtOrden = $db->prepare("
                INSERT INTO ordenes (
                    empresa_id,
                    vehiculo_id,
                    cliente_id,
                    codigo,
                    origen,
                    cotizacion_id,
                    descripcion,
                    total,
                    estado,
                    pago_estado,
                    adelanto,
                    saldo
                ) VALUES (
                    :empresa_id,
                    :vehiculo_id,
                    :cliente_id,
                    :codigo,
                    'DIRECTA',
                    NULL,
                    :descripcion,
                    :total,
                    'PENDIENTE',
                    :pago_estado,
                    :adelanto,
                    :saldo
                )
            ");

            $stmtOrden->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtOrden->bindParam(':vehiculo_id', $vehiculo['vehiculo_id'], PDO::PARAM_INT);
            $stmtOrden->bindParam(':cliente_id', $vehiculo['cliente_id'], PDO::PARAM_INT);
            $stmtOrden->bindParam(':codigo', $codigo);
            $stmtOrden->bindParam(':descripcion', $descripcionOrden);
            $stmtOrden->bindParam(':total', $total);
            $stmtOrden->bindParam(':pago_estado', $pago_estado);
            $stmtOrden->bindParam(':adelanto', $adelanto);
            $stmtOrden->bindParam(':saldo', $saldo);
            $stmtOrden->execute();

            $orden_id = $db->lastInsertId();

            foreach ($itemsLimpios as $item) {
                $stmtDetalle = $db->prepare("
                    INSERT INTO orden_detalle (
                        empresa_id,
                        orden_id,
                        producto_id,
                        tipo_item,
                        descripcion,
                        cantidad,
                        costo_unitario,
                        precio_unitario,
                        subtotal
                    ) VALUES (
                        :empresa_id,
                        :orden_id,
                        :producto_id,
                        :tipo_item,
                        :descripcion,
                        :cantidad,
                        :costo_unitario,
                        :precio_unitario,
                        :subtotal
                    )
                ");

                $stmtDetalle->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
                $stmtDetalle->bindParam(':orden_id', $orden_id, PDO::PARAM_INT);
                $stmtDetalle->bindParam(':producto_id', $item['producto_id']);
                $stmtDetalle->bindParam(':tipo_item', $item['tipo_item']);
                $stmtDetalle->bindParam(':descripcion', $item['descripcion']);
                $stmtDetalle->bindParam(':cantidad', $item['cantidad']);
                $stmtDetalle->bindParam(':costo_unitario', $item['costo_unitario']);
                $stmtDetalle->bindParam(':precio_unitario', $item['precio_unitario']);
                $stmtDetalle->bindParam(':subtotal', $item['subtotal']);
                $stmtDetalle->execute();

                if ($item['tipo_item'] === 'STOCK') {
                    $stmtStock = $db->prepare("
                        UPDATE productos
                        SET stock_actual = stock_actual - :cantidad
                        WHERE id = :producto_id
                        AND empresa_id = :empresa_id
                    ");

                    $stmtStock->bindParam(':cantidad', $item['cantidad']);
                    $stmtStock->bindParam(':producto_id', $item['producto_id'], PDO::PARAM_INT);
                    $stmtStock->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
                    $stmtStock->execute();
                }
            }

            $db->commit();

            return [
                'ok' => true,
                'orden_id' => $orden_id
            ];

        } catch (Exception $e) {
            $db->rollBack();

            return [
                'ok' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public static function obtenerOrden($empresa_id, $orden_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT 
                o.*,
                v.placa,
                v.marca,
                v.modelo,
                c.nombre AS cliente,
                c.telefono
            FROM ordenes o
            INNER JOIN vehiculos v ON v.id = o.vehiculo_id
            INNER JOIN clientes c ON c.id = o.cliente_id
            WHERE o.empresa_id = :empresa_id
            AND o.id = :orden_id
            LIMIT 1
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':orden_id', $orden_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerDetalle($empresa_id, $orden_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT *
            FROM orden_detalle
            WHERE empresa_id = :empresa_id
            AND orden_id = :orden_id
            ORDER BY id ASC
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':orden_id', $orden_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>