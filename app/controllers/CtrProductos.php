<?php
require_once __DIR__ . '/../models/MdProductos.php';

class CtrProductos
{
    public static function listar()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $buscar = trim($_GET['buscar'] ?? '');
        $categoria = trim($_GET['categoria'] ?? '');

        return MdProductos::listar($empresa_id, $buscar, $categoria);
    }

    public static function categorias()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        return MdProductos::categorias($empresa_id);
    }

    public static function resumen()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'total_productos' => 0,
                'bajo_stock' => 0,
                'valorizado_costo' => 0
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        return MdProductos::resumen($empresa_id);
    }

    public static function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return [
                'ok' => null,
                'mensaje' => ''
            ];
        }

        if (!isset($_SESSION['empresa_id'])) {
            return [
                'ok' => false,
                'mensaje' => 'Sesión inválida.'
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        $datos = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'categoria' => trim($_POST['categoria'] ?? 'Otros'),
            'unidad' => trim($_POST['unidad'] ?? 'UND'),
            'stock_actual' => (float) ($_POST['stock_actual'] ?? 0),
            'stock_minimo' => (float) ($_POST['stock_minimo'] ?? 0),
            'costo' => (float) ($_POST['costo'] ?? 0),
            'precio_venta' => (float) ($_POST['precio_venta'] ?? 0)
        ];

        if ($datos['nombre'] === '') {
            return [
                'ok' => false,
                'mensaje' => 'El nombre del producto es obligatorio.'
            ];
        }

        $guardado = MdProductos::guardar($empresa_id, $datos);

        if ($guardado) {
            header("Location: productos.php?ok=1");
            exit;
        }

        return [
            'ok' => false,
            'mensaje' => 'No se pudo guardar el producto.'
        ];
    }
}
?>