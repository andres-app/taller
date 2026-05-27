<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

require_once '../app/controllers/CtrCotizaciones.php';

$respuesta = CtrCotizaciones::convertir();

$id = (int)($_POST['cotizacion_id'] ?? $_GET['id'] ?? 0);

if ($respuesta['ok'] === false) {
    $_SESSION['error_convertir'] = $respuesta['mensaje'];
    header("Location: detalle_cotizacion.php?id=" . $id);
    exit;
}

header("Location: detalle_cotizacion.php?id=" . $id);
exit;
?>