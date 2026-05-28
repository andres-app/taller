<?php
require_once __DIR__ . '/../models/MdClientes.php';

class CtrClientes
{
    public static function listar()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $buscar = trim($_GET['buscar'] ?? '');

        return MdClientes::listar($empresa_id, $buscar);
    }

    public static function resumen()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'total_clientes' => 0,
                'con_whatsapp' => 0,
                'deuda_total' => 0
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        return MdClientes::resumen($empresa_id);
    }

    public static function detalle()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'cliente' => null,
                'vehiculos' => [],
                'ordenes' => [],
                'cotizaciones' => []
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];
        $cliente_id = (int)($_GET['id'] ?? 0);

        if ($cliente_id <= 0) {
            return [
                'cliente' => null,
                'vehiculos' => [],
                'ordenes' => [],
                'cotizaciones' => []
            ];
        }

        return [
            'cliente' => MdClientes::obtener($empresa_id, $cliente_id),
            'vehiculos' => MdClientes::vehiculos($empresa_id, $cliente_id),
            'ordenes' => MdClientes::ordenes($empresa_id, $cliente_id),
            'cotizaciones' => MdClientes::cotizaciones($empresa_id, $cliente_id)
        ];
    }

    public static function actualizar()
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
        $cliente_id = (int)($_POST['cliente_id'] ?? 0);

        if ($cliente_id <= 0) {
            return [
                'ok' => false,
                'mensaje' => 'Cliente inválido.'
            ];
        }

        $datos = [
            'nombre' => trim($_POST['nombre'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? '')
        ];

        if ($datos['nombre'] === '') {
            return [
                'ok' => false,
                'mensaje' => 'El nombre del cliente es obligatorio.'
            ];
        }

        $actualizado = MdClientes::actualizar($empresa_id, $cliente_id, $datos);

        if ($actualizado) {
            header("Location: detalle_cliente.php?id=" . $cliente_id . "&editado=ok");
            exit;
        }

        return [
            'ok' => false,
            'mensaje' => 'No se pudo actualizar el cliente.'
        ];
    }
}
