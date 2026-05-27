<?php
require_once __DIR__ . '/../../config/database.php';

class MdVehiculos
{
    public static function buscarPorPlaca($empresa_id, $placa)
    {
        $database = new Database();
        $db = $database->connect();

        $placa = strtoupper(trim(str_replace(['-', ' '], '', $placa)));

        $stmt = $db->prepare("
            SELECT 
                v.id,
                v.empresa_id,
                v.cliente_id,
                v.placa,
                v.marca,
                v.modelo,
                v.anio,
                v.color,
                v.kilometraje,
                v.observaciones,
                c.nombre AS cliente,
                c.telefono,
                c.direccion
            FROM vehiculos v
            INNER JOIN clientes c ON c.id = v.cliente_id
            WHERE v.empresa_id = :empresa_id
            AND REPLACE(REPLACE(UPPER(v.placa), '-', ''), ' ', '') = :placa
            LIMIT 1
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':placa', $placa);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function historialOrdenes($empresa_id, $vehiculo_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT 
                id,
                descripcion,
                total,
                estado,
                fecha_registro
            FROM ordenes
            WHERE empresa_id = :empresa_id
            AND vehiculo_id = :vehiculo_id
            ORDER BY fecha_registro DESC, id DESC
            LIMIT 10
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':vehiculo_id', $vehiculo_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>