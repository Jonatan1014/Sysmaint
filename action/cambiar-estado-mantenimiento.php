<?php
// action/cambiar-estado-mantenimiento.php
session_start();
require_once '../includes/Class-mantenimientos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Permitir tanto POST como GET para facilidad de uso
    $id_mantenimiento = $_POST['id'] ?? $_GET['id'] ?? null;
    $id_estado = $_POST['estado'] ?? $_GET['estado'] ?? null;
    $origen = $_POST['origen'] ?? $_GET['origen'] ?? 'mantenimientos';

    if (empty($id_mantenimiento) || empty($id_estado)) {
        $_SESSION['error'] = 'Datos incompletos para cambiar el estado.';
        header("Location: ../" . $origen . ".php");
        exit;
    }

    $Mantenimiento_class = new Mantenimiento();

    try {
        $actualizado = $Mantenimiento_class->cambiarEstado($id_mantenimiento, $id_estado);

        if ($actualizado) {
            $_SESSION['exito'] = 'Estado del mantenimiento actualizado correctamente.';
        } else {
            $_SESSION['error'] = 'Error al actualizar el estado del mantenimiento.';
        }

    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }

    // Redirigir según el origen
    if ($origen === 'detalle') {
        header("Location: ../mantenimiento-detalle.php?id=" . $id_mantenimiento);
    } else {
        header("Location: ../" . $origen . ".php");
    }
    exit;

} else {
    $_SESSION['error'] = 'Acceso no válido.';
    header("Location: ../mantenimientos.php");
    exit;
}
?>
