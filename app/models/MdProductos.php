<?php
require_once __DIR__ . '/../../config/database.php';

class MdProductos
{
    public static function listar($empresa_id, $buscar = '', $categoria = '')
    {
        $database = new Database();
        $db = $database->connect();

        $sql = "
            SELECT *
            FROM productos
            WHERE empresa_id = :empresa_id
            AND estado = 'ACTIVO'
        ";

        if ($buscar !== '') {
            $sql .= " AND nombre LIKE :buscar ";
        }

        if ($categoria !== '') {
            $sql .= " AND categoria = :categoria ";
        }

        $sql .= " ORDER BY categoria ASC, nombre ASC ";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);

        if ($buscar !== '') {
            $buscarLike = '%' . $buscar . '%';
            $stmt->bindParam(':buscar', $buscarLike);
        }

        if ($categoria !== '') {
            $stmt->bindParam(':categoria', $categoria);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function categorias($empresa_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT DISTINCT categoria
            FROM productos
            WHERE empresa_id = :empresa_id
            AND estado = 'ACTIVO'
            ORDER BY categoria ASC
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function guardar($empresa_id, $datos)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            INSERT INTO productos (
                empresa_id,
                nombre,
                categoria,
                unidad,
                stock_actual,
                stock_minimo,
                costo,
                precio_venta,
                estado
            ) VALUES (
                :empresa_id,
                :nombre,
                :categoria,
                :unidad,
                :stock_actual,
                :stock_minimo,
                :costo,
                :precio_venta,
                'ACTIVO'
            )
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $datos['nombre']);
        $stmt->bindParam(':categoria', $datos['categoria']);
        $stmt->bindParam(':unidad', $datos['unidad']);
        $stmt->bindParam(':stock_actual', $datos['stock_actual']);
        $stmt->bindParam(':stock_minimo', $datos['stock_minimo']);
        $stmt->bindParam(':costo', $datos['costo']);
        $stmt->bindParam(':precio_venta', $datos['precio_venta']);

        return $stmt->execute();
    }

    public static function resumen($empresa_id)
    {
        $database = new Database();
        $db = $database->connect();

        $stmt = $db->prepare("
            SELECT
                COUNT(*) AS total_productos,
                SUM(CASE WHEN stock_actual <= stock_minimo THEN 1 ELSE 0 END) AS bajo_stock,
                SUM(stock_actual * costo) AS valorizado_costo
            FROM productos
            WHERE empresa_id = :empresa_id
            AND estado = 'ACTIVO'
        ");

        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>