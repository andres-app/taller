<?php
require_once __DIR__ . '/../../config/database.php';

class MdClientes
{
    public static function listar($empresa_id, $buscar = '')
    {
        $database = new Database();
        $db = $database->connect();

        $sql = "
            SELECT 
                c.id,
                c.nombre,
                c.telefono,
                c.direccion,
                COUNT(DISTINCT v.id) AS total_vehiculos,
                COUNT(DISTINCT o.id) AS total_ordenes,
                COALESCE(SUM(o.saldo), 0) AS deuda_total
            FROM clientes c
            LEFT JOIN vehiculos v 
                ON v.cliente_id = c.id 
                AND v.empresa_id = c.empresa_id
            LEFT JOIN ordenes o 
                ON o.cliente_id = c.id 
                AND o.empresa_id = c.empresa_id
            WHERE c.empresa_id = :empresa_id
        ";

        if ($buscar !== '') {
            $sql .= "
                AND (
                    c.nombre LIKE :buscar
                    OR c.telefono LIKE :buscar
                    OR c.direccion LIKE :buscar
                )
            ";
        }

        $sql .= "
            GROUP BY c.id, c.nombre, c.telefono, c.direccion
            ORDER BY c.nombre ASC
        ";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);

        if ($buscar !== '') {
            $buscarLike = '%' . $buscar . '%';
            $stmt->bindParam(':buscar', $buscarLike);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function resumen($empresa_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT
                COUNT(*) AS total_clientes,
                SUM(CASE WHEN telefono IS NOT NULL AND telefono <> '' THEN 1 ELSE 0 END) AS con_whatsapp
            FROM clientes
            WHERE empresa_id = :empresa_id
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        $resumen = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmtDeuda = $db->prepare("
            SELECT COALESCE(SUM(saldo), 0) AS deuda_total
            FROM ordenes
            WHERE empresa_id = :empresa_id
            AND saldo > 0
        ");

        $stmtDeuda->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmtDeuda->execute();

        $deuda = $stmtDeuda->fetch(PDO::FETCH_ASSOC);

        $resumen['deuda_total'] = $deuda['deuda_total'] ?? 0;

        return $resumen;
    }

    public static function obtener($empresa_id, $cliente_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT *
            FROM clientes
            WHERE empresa_id = :empresa_id
            AND id = :cliente_id
            LIMIT 1
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function vehiculos($empresa_id, $cliente_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT *
            FROM vehiculos
            WHERE empresa_id = :empresa_id
            AND cliente_id = :cliente_id
            ORDER BY id DESC
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function ordenes($empresa_id, $cliente_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT 
                o.*,
                v.placa,
                v.marca,
                v.modelo
            FROM ordenes o
            INNER JOIN vehiculos v ON v.id = o.vehiculo_id
            WHERE o.empresa_id = :empresa_id
            AND o.cliente_id = :cliente_id
            ORDER BY o.fecha_registro DESC, o.id DESC
            LIMIT 20
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function cotizaciones($empresa_id, $cliente_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT 
                co.*,
                v.placa,
                v.marca,
                v.modelo
            FROM cotizaciones co
            INNER JOIN vehiculos v ON v.id = co.vehiculo_id
            WHERE co.empresa_id = :empresa_id
            AND co.cliente_id = :cliente_id
            ORDER BY co.created_at DESC, co.id DESC
            LIMIT 20
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actualizar($empresa_id, $cliente_id, $datos)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
        UPDATE clientes
        SET 
            nombre = :nombre,
            telefono = :telefono,
            direccion = :direccion
        WHERE empresa_id = :empresa_id
        AND id = :cliente_id
    ");

        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':cliente_id', $cliente_id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
