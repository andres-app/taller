<?php
require_once __DIR__ . '/../models/MdOrdenes.php';

class CtrOrdenes
{
    public static function datosNueva()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'vehiculo' => null,
                'productos' => []
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $vehiculo_id = (int) ($_GET['vehiculo_id'] ?? $_POST['vehiculo_id'] ?? 0);

        if ($vehiculo_id <= 0) {
            return [
                'vehiculo' => null,
                'productos' => []
            ];
        }

        return [
            'vehiculo' => MdOrdenes::obtenerVehiculo($empresa_id, $vehiculo_id),
            'productos' => MdOrdenes::listarProductos($empresa_id)
        ];
    }

    public static function guardarDetallada()
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
        $vehiculo_id = (int) ($_POST['vehiculo_id'] ?? 0);

        if ($vehiculo_id <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Vehículo inválido.'
            ];
        }

        $vehiculo = MdOrdenes::obtenerVehiculo($empresa_id, $vehiculo_id);

        if (!$vehiculo) {
            return [
                'ok' => false,
                'mensaje' => 'No se encontró el vehículo.'
            ];
        }

        $datos = [
            'descripcion' => trim($_POST['descripcion'] ?? 'Orden detallada'),
            'pago_estado' => trim($_POST['pago_estado'] ?? 'PAGADO'),
            'adelanto' => (float)($_POST['adelanto'] ?? 0)
        ];

        $items = $_POST['items'] ?? [];

        $respuesta = MdOrdenes::guardarOrdenDirecta($empresa_id, $vehiculo, $datos, $items);

        if ($respuesta['ok']) {
            header("Location: detalle_orden.php?id=" . $respuesta['orden_id']);
            exit;
        }

        return [
            'ok' => false,
            'mensaje' => $respuesta['error']
        ];
    }

    public static function detalle()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'orden' => null,
                'detalle' => []
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $orden_id = (int) ($_GET['id'] ?? 0);

        if ($orden_id <= 0) {
            return [
                'orden' => null,
                'detalle' => []
            ];
        }

        return [
            'orden' => MdOrdenes::obtenerOrden($empresa_id, $orden_id),
            'detalle' => MdOrdenes::obtenerDetalle($empresa_id, $orden_id)
        ];
    }
}
?>