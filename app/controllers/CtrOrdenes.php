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

    public static function listar()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $buscar = trim($_GET['buscar'] ?? '');
        $estado = trim($_GET['estado'] ?? '');

        return MdOrdenes::listarOrdenes($empresa_id, $buscar, $estado);
    }

    public static function resumen()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'total_ordenes' => 0,
                'pendientes' => 0,
                'finalizadas' => 0,
                'total_fiado' => 0,
                'total_general' => 0
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        return MdOrdenes::resumenOrdenes($empresa_id);
    }

    public static function deudas()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $buscar = trim($_GET['buscar'] ?? '');

        return MdOrdenes::listarDeudas($empresa_id, $buscar);
    }

    public static function resumenDeudas()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'total_deudas' => 0,
                'total_saldo' => 0,
                'total_original' => 0,
                'total_pagado' => 0
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        return MdOrdenes::resumenDeudas($empresa_id);
    }

    public static function registrarPago()
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
        $orden_id = (int)($_POST['orden_id'] ?? 0);

        if ($orden_id <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Orden inválida.'
            ];
        }

        $datos = [
            'monto' => (float)($_POST['monto'] ?? 0),
            'metodo' => trim($_POST['metodo'] ?? 'EFECTIVO'),
            'observacion' => trim($_POST['observacion'] ?? '')
        ];

        $respuesta = MdOrdenes::registrarPago($empresa_id, $orden_id, $datos);

        if ($respuesta['ok']) {
            header("Location: detalle_orden.php?id=" . $orden_id . "&pago=ok");
            exit;
        }

        return [
            'ok' => false,
            'mensaje' => $respuesta['error']
        ];
    }

    public static function pagosOrden($orden_id)
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        return MdOrdenes::obtenerPagosOrden($empresa_id, $orden_id);
    }
}
