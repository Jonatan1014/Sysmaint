<?php
// action/delete-vehiculos.php
require_once '../includes/Class-vehiculos.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $id = $_POST['id'];
    $Vehiculo_class = new Vehiculo();
    
    try {
        $resultado = $Vehiculo_class->eliminarVehiculo($id);
        
        if ($resultado) {
            $_SESSION['exito'] = 'Vehículo eliminado correctamente';
        } else {
            $_SESSION['error'] = 'Error al eliminar el vehículo.';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID de vehículo no proporcionado.';
}

header("Location: ../admin.php");
exit;
?>