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

    public static function convertirEnOrden($empresa_id, $cotizacion_id)
    {
        $database = new Database();
        $db = $database->connect();

        try {
            $db->beginTransaction();

            $stmtCot = $db->prepare("
                SELECT *
                FROM cotizaciones
                WHERE id = :id
                AND empresa_id = :empresa_id
                LIMIT 1
            ");

            $stmtCot->bindParam(':id', $cotizacion_id, PDO::PARAM_INT);
            $stmtCot->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtCot->execute();

            $cotizacion = $stmtCot->fetch(PDO::FETCH_ASSOC);

            if (!$cotizacion) {
                throw new Exception('Cotización no encontrada.');
            }

            if ($cotizacion['estado'] === 'CONVERTIDA') {
                throw new Exception('Esta cotización ya fue convertida en orden.');
            }

            $stmtDetalle = $db->prepare("
                SELECT *
                FROM cotizacion_detalle
                WHERE cotizacion_id = :cotizacion_id
                AND empresa_id = :empresa_id
                ORDER BY id ASC
            ");

            $stmtDetalle->bindParam(':cotizacion_id', $cotizacion_id, PDO::PARAM_INT);
            $stmtDetalle->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtDetalle->execute();

            $detalle = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

            if (count($detalle) === 0) {
                throw new Exception('La cotización no tiene detalle.');
            }

            foreach ($detalle as $item) {
                if ($item['tipo_item'] === 'STOCK') {
                    if (empty($item['producto_id'])) {
                        throw new Exception('Hay un item de stock sin producto asociado.');
                    }

                    $stmtStock = $db->prepare("
                        SELECT nombre, stock_actual
                        FROM productos
                        WHERE id = :producto_id
                        AND empresa_id = :empresa_id
                        LIMIT 1
                        FOR UPDATE
                    ");

                    $stmtStock->bindParam(':producto_id', $item['producto_id'], PDO::PARAM_INT);
                    $stmtStock->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
                    $stmtStock->execute();

                    $producto = $stmtStock->fetch(PDO::FETCH_ASSOC);

                    if (!$producto) {
                        throw new Exception('Producto no encontrado en inventario.');
                    }

                    if ((float)$producto['stock_actual'] < (float)$item['cantidad']) {
                        throw new Exception(
                            'Stock insuficiente para ' . $producto['nombre'] .
                            '. Disponible: ' . $producto['stock_actual'] .
                            ', requerido: ' . $item['cantidad']
                        );
                    }
                }
            }

            $codigoOrden = 'ORD-' . date('YmdHis');

            $descripcion = 'Orden creada desde cotización ' . $cotizacion['codigo'];

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
                    'COTIZACION',
                    :cotizacion_id,
                    :descripcion,
                    :total,
                    'PENDIENTE',
                    'PAGADO',
                    0,
                    0
                )
            ");

            $stmtOrden->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtOrden->bindParam(':vehiculo_id', $cotizacion['vehiculo_id'], PDO::PARAM_INT);
            $stmtOrden->bindParam(':cliente_id', $cotizacion['cliente_id'], PDO::PARAM_INT);
            $stmtOrden->bindParam(':codigo', $codigoOrden);
            $stmtOrden->bindParam(':cotizacion_id', $cotizacion_id, PDO::PARAM_INT);
            $stmtOrden->bindParam(':descripcion', $descripcion);
            $stmtOrden->bindParam(':total', $cotizacion['total']);
            $stmtOrden->execute();

            $orden_id = $db->lastInsertId();

            foreach ($detalle as $item) {
                $stmtOrdenDetalle = $db->prepare("
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

                $stmtOrdenDetalle->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
                $stmtOrdenDetalle->bindParam(':orden_id', $orden_id, PDO::PARAM_INT);
                $stmtOrdenDetalle->bindParam(':producto_id', $item['producto_id']);
                $stmtOrdenDetalle->bindParam(':tipo_item', $item['tipo_item']);
                $stmtOrdenDetalle->bindParam(':descripcion', $item['descripcion']);
                $stmtOrdenDetalle->bindParam(':cantidad', $item['cantidad']);
                $stmtOrdenDetalle->bindParam(':costo_unitario', $item['costo_unitario']);
                $stmtOrdenDetalle->bindParam(':precio_unitario', $item['precio_unitario']);
                $stmtOrdenDetalle->bindParam(':subtotal', $item['subtotal']);
                $stmtOrdenDetalle->execute();

                if ($item['tipo_item'] === 'STOCK') {
                    $stmtDescontar = $db->prepare("
                        UPDATE productos
                        SET stock_actual = stock_actual - :cantidad
                        WHERE id = :producto_id
                        AND empresa_id = :empresa_id
                    ");

                    $stmtDescontar->bindParam(':cantidad', $item['cantidad']);
                    $stmtDescontar->bindParam(':producto_id', $item['producto_id'], PDO::PARAM_INT);
                    $stmtDescontar->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
                    $stmtDescontar->execute();
                }
            }

            $stmtUpdateCot = $db->prepare("
                UPDATE cotizaciones
                SET estado = 'CONVERTIDA',
                    orden_id = :orden_id
                WHERE id = :cotizacion_id
                AND empresa_id = :empresa_id
            ");

            $stmtUpdateCot->bindParam(':orden_id', $orden_id, PDO::PARAM_INT);
            $stmtUpdateCot->bindParam(':cotizacion_id', $cotizacion_id, PDO::PARAM_INT);
            $stmtUpdateCot->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtUpdateCot->execute();

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

    public static function listarCotizaciones($empresa_id, $buscar = '', $estado = '')
{
    $database = new Database();
    $db = $database->connect();

    $sql = "
        SELECT 
            co.id,
            co.codigo,
            co.total,
            co.estado,
            co.created_at,
            co.orden_id,
            v.placa,
            v.marca,
            v.modelo,
            c.nombre AS cliente,
            c.telefono
        FROM cotizaciones co
        INNER JOIN vehiculos v ON v.id = co.vehiculo_id
        INNER JOIN clientes c ON c.id = co.cliente_id
        WHERE co.empresa_id = :empresa_id
    ";

    if ($buscar !== '') {
        $sql .= "
            AND (
                co.codigo LIKE :buscar
                OR v.placa LIKE :buscar
                OR c.nombre LIKE :buscar
                OR c.telefono LIKE :buscar
            )
        ";
    }

    if ($estado !== '') {
        $sql .= " AND co.estado = :estado ";
    }

    $sql .= " ORDER BY co.created_at DESC, co.id DESC ";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);

    if ($buscar !== '') {
        $buscarLike = '%' . $buscar . '%';
        $stmt->bindParam(':buscar', $buscarLike);
    }

    if ($estado !== '') {
        $stmt->bindParam(':estado', $estado);
    }

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public static function resumenCotizaciones($empresa_id)
{
    $database = new Database();
    $db = $database->connect();

    $stmt = $db->prepare("
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN estado = 'PENDIENTE' THEN 1 ELSE 0 END) AS pendientes,
            SUM(CASE WHEN estado = 'CONVERTIDA' THEN 1 ELSE 0 END) AS convertidas,
            SUM(CASE WHEN estado = 'PENDIENTE' THEN total ELSE 0 END) AS monto_pendiente
        FROM cotizaciones
        WHERE empresa_id = :empresa_id
    ");

    $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
?>