<?php
require_once __DIR__ . '/../../config/database.php';

class MdRegistroVehiculo
{
    public static function guardarClienteVehiculo($empresa_id, $datos)
    {
        $database = new Database();
        $db = $database->connect();

        try {
            $db->beginTransaction();

            $stmtCliente = $db->prepare("
                INSERT INTO clientes (
                    empresa_id,
                    nombre,
                    telefono,
                    direccion
                ) VALUES (
                    :empresa_id,
                    :nombre,
                    :telefono,
                    :direccion
                )
            ");

            $stmtCliente->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtCliente->bindParam(':nombre', $datos['nombre']);
            $stmtCliente->bindParam(':telefono', $datos['telefono']);
            $stmtCliente->bindParam(':direccion', $datos['direccion']);
            $stmtCliente->execute();

            $cliente_id = $db->lastInsertId();

            $placa = strtoupper(trim(str_replace(['-', ' '], '', $datos['placa'])));

            $stmtVehiculo = $db->prepare("
                INSERT INTO vehiculos (
                    empresa_id,
                    cliente_id,
                    placa,
                    marca,
                    modelo,
                    anio,
                    color,
                    kilometraje,
                    observaciones
                ) VALUES (
                    :empresa_id,
                    :cliente_id,
                    :placa,
                    :marca,
                    :modelo,
                    :anio,
                    :color,
                    :kilometraje,
                    :observaciones
                )
            ");

            $stmtVehiculo->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtVehiculo->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
            $stmtVehiculo->bindParam(':placa', $placa);
            $stmtVehiculo->bindParam(':marca', $datos['marca']);
            $stmtVehiculo->bindParam(':modelo', $datos['modelo']);
            $stmtVehiculo->bindParam(':anio', $datos['anio']);
            $stmtVehiculo->bindParam(':color', $datos['color']);
            $stmtVehiculo->bindParam(':kilometraje', $datos['kilometraje']);
            $stmtVehiculo->bindParam(':observaciones', $datos['observaciones']);
            $stmtVehiculo->execute();

            $db->commit();

            return [
                'ok' => true,
                'placa' => $placa
            ];

        } catch (Exception $e) {
            $db->rollBack();

            return [
                'ok' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public static function placaExiste($empresa_id, $placa)
    {
        $database = new Database();
        $db = $database->connect();

        $placa = strtoupper(trim(str_replace(['-', ' '], '', $placa)));

        $stmt = $db->prepare("
            SELECT id
            FROM vehiculos
            WHERE empresa_id = :empresa_id
            AND REPLACE(REPLACE(UPPER(placa), '-', ''), ' ', '') = :placa
            LIMIT 1
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':placa', $placa);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>