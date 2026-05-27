<?php
require_once __DIR__ . '/../../config/database.php';

class MdOrdenes
{
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