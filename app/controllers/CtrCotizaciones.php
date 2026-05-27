<?php
require_once __DIR__ . '/../models/MdCotizaciones.php';

class CtrCotizaciones
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
            'vehiculo' => MdCotizaciones::obtenerVehiculo($empresa_id, $vehiculo_id),
            'productos' => MdCotizaciones::listarProductos($empresa_id)
        ];
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
        $vehiculo_id = (int) ($_POST['vehiculo_id'] ?? 0);

        if ($vehiculo_id <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Vehículo inválido.'
            ];
        }

        $vehiculo = MdCotizaciones::obtenerVehiculo($empresa_id, $vehiculo_id);

        if (!$vehiculo) {
            return [
                'ok' => false,
                'mensaje' => 'No se encontró el vehículo.'
            ];
        }

        $datos = [
            'observacion' => trim($_POST['observacion'] ?? '')
        ];

        $items = $_POST['items'] ?? [];

        $respuesta = MdCotizaciones::guardar($empresa_id, $vehiculo, $datos, $items);

        if ($respuesta['ok']) {
            header("Location: detalle_cotizacion.php?id=" . $respuesta['id']);
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
                'cotizacion' => null,
                'detalle' => []
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $cotizacion_id = (int) ($_GET['id'] ?? 0);

        if ($cotizacion_id <= 0) {
            return [
                'cotizacion' => null,
                'detalle' => []
            ];
        }

        return [
            'cotizacion' => MdCotizaciones::obtenerCotizacion($empresa_id, $cotizacion_id),
            'detalle' => MdCotizaciones::obtenerDetalle($empresa_id, $cotizacion_id)
        ];
    }

    public static function convertir()
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
        $cotizacion_id = (int) ($_POST['cotizacion_id'] ?? 0);

        if ($cotizacion_id <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Cotización inválida.'
            ];
        }

        $respuesta = MdCotizaciones::convertirEnOrden($empresa_id, $cotizacion_id);

        if ($respuesta['ok']) {
            header("Location: detalle_orden.php?id=" . $respuesta['orden_id']);
            exit;
        }

        return [
            'ok' => false,
            'mensaje' => $respuesta['error']
        ];
    }

    public static function listar()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $buscar = trim($_GET['buscar'] ?? '');
        $estado = trim($_GET['estado'] ?? '');

        return MdCotizaciones::listarCotizaciones($empresa_id, $buscar, $estado);
    }

    public static function resumen()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'total' => 0,
                'pendientes' => 0,
                'convertidas' => 0,
                'monto_pendiente' => 0
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        return MdCotizaciones::resumenCotizaciones($empresa_id);
    }
}
