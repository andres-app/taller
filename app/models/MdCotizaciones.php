<?php
require_once __DIR__ . '/../../config/database.php';

class MdCotizaciones
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

    public static function guardar($empresa_id, $vehiculo, $datos, $items)
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

                if ($tipo === 'STOCK' && $producto_id) {
                    $stmtProducto = $db->prepare("
                        SELECT nombre, costo, precio_venta
                        FROM productos
                        WHERE id = :id
                        AND empresa_id = :empresa_id
                        LIMIT 1
                    ");

                    $stmtProducto->bindParam(':id', $producto_id, PDO::PARAM_INT);
                    $stmtProducto->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
                    $stmtProducto->execute();

                    $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

                    if ($producto) {
                        $descripcion = $producto['nombre'];
                        $costo = (float)$producto['costo'];
                        $precio = (float)$producto['precio_venta'];
                    }
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
                throw new Exception('Debes agregar al menos un item a la cotización.');
            }

            $codigo = 'COT-' . date('YmdHis');

            $stmt = $db->prepare("
                INSERT INTO cotizaciones (
                    empresa_id,
                    vehiculo_id,
                    cliente_id,
                    codigo,
                    observacion,
                    total,
                    estado
                ) VALUES (
                    :empresa_id,
                    :vehiculo_id,
                    :cliente_id,
                    :codigo,
                    :observacion,
                    :total,
                    'PENDIENTE'
                )
            ");

            $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmt->bindParam(':vehiculo_id', $vehiculo['vehiculo_id'], PDO::PARAM_INT);
            $stmt->bindParam(':cliente_id', $vehiculo['cliente_id'], PDO::PARAM_INT);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':observacion', $datos['observacion']);
            $stmt->bindParam(':total', $total);
            $stmt->execute();

            $cotizacion_id = $db->lastInsertId();

            foreach ($itemsLimpios as $item) {
                $stmtDetalle = $db->prepare("
                    INSERT INTO cotizacion_detalle (
                        empresa_id,
                        cotizacion_id,
                        producto_id,
                        tipo_item,
                        descripcion,
                        cantidad,
                        costo_unitario,
                        precio_unitario,
                        subtotal
                    ) VALUES (
                        :empresa_id,
                        :cotizacion_id,
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
                $stmtDetalle->bindParam(':cotizacion_id', $cotizacion_id, PDO::PARAM_INT);
                $stmtDetalle->bindParam(':producto_id', $item['producto_id']);
                $stmtDetalle->bindParam(':tipo_item', $item['tipo_item']);
                $stmtDetalle->bindParam(':descripcion', $item['descripcion']);
                $stmtDetalle->bindParam(':cantidad', $item['cantidad']);
                $stmtDetalle->bindParam(':costo_unitario', $item['costo_unitario']);
                $stmtDetalle->bindParam(':precio_unitario', $item['precio_unitario']);
                $stmtDetalle->bindParam(':subtotal', $item['subtotal']);
                $stmtDetalle->execute();
            }

            $db->commit();

            return [
                'ok' => true,
                'id' => $cotizacion_id
            ];

        } catch (Exception $e) {
            $db->rollBack();

            return [
                'ok' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public static function obtenerCotizacion($empresa_id, $cotizacion_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT 
                co.*,
                v.placa,
                v.marca,
                v.modelo,
                c.nombre AS cliente,
                c.telefono
            FROM cotizaciones co
            INNER JOIN vehiculos v ON v.id = co.vehiculo_id
            INNER JOIN clientes c ON c.id = co.cliente_id
            WHERE co.empresa_id = :empresa_id
            AND co.id = :cotizacion_id
            LIMIT 1
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':cotizacion_id', $cotizacion_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerDetalle($empresa_id, $cotizacion_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT *
            FROM cotizacion_detalle
            WHERE empresa_id = :empresa_id
            AND cotizacion_id = :cotizacion_id
            ORDER BY id ASC
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':cotizacion_id', $cotizacion_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>