<?php
require_once __DIR__ . '/../models/MdVehiculos.php';

class CtrVehiculos
{
    public static function buscar()
    {
        if (!isset($_SESSION['empresa_id'])) {
            return [
                'buscado' => false,
                'vehiculo' => null,
                'historial' => []
            ];
        }

        $placa = $_GET['placa'] ?? '';

        if (trim($placa) === '') {
            return [
                'buscado' => false,
                'vehiculo' => null,
                'historial' => []
            ];
        }

        $empresa_id = (int) $_SESSION['empresa_id'];

        $vehiculo = MdVehiculos::buscarPorPlaca($empresa_id, $placa);

        if ($vehiculo) {
            $historial = MdVehiculos::historialOrdenes($empresa_id, (int) $vehiculo['id']);
        } else {
            $historial = [];
        }

        return [
            'buscado' => true,
            'vehiculo' => $vehiculo,
            'historial' => $historial
        ];
    }
}
?>