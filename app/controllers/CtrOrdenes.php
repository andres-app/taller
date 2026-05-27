<?php
require_once __DIR__ . '/../models/MdOrdenes.php';

class CtrOrdenes
{
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