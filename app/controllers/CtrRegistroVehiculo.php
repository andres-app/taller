<?php
require_once __DIR__ . '/../models/MdRegistroVehiculo.php';

class CtrRegistroVehiculo
{
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
            'telefono' => trim($_POST['telefono'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'placa' => strtoupper(trim($_POST['placa'] ?? '')),
            'marca' => trim($_POST['marca'] ?? ''),
            'modelo' => trim($_POST['modelo'] ?? ''),
            'anio' => trim($_POST['anio'] ?? ''),
            'color' => trim($_POST['color'] ?? ''),
            'kilometraje' => trim($_POST['kilometraje'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? '')
        ];

        if ($datos['nombre'] === '' || $datos['placa'] === '') {
            return [
                'ok' => false,
                'mensaje' => 'El nombre del cliente y la placa son obligatorios.'
            ];
        }

        $placaExiste = MdRegistroVehiculo::placaExiste($empresa_id, $datos['placa']);

        if ($placaExiste) {
            return [
                'ok' => false,
                'mensaje' => 'Esta placa ya existe en el sistema.'
            ];
        }

        $respuesta = MdRegistroVehiculo::guardarClienteVehiculo($empresa_id, $datos);

        if ($respuesta['ok']) {
            header("Location: vehiculos.php?placa=" . urlencode($respuesta['placa']));
            exit;
        }

        return [
            'ok' => false,
            'mensaje' => 'No se pudo registrar: ' . $respuesta['error']
        ];
    }
}
?>