<?php
// action/update-mantenimientos.php
session_start();
require_once '../includes/Class-mantenimientos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    // Validar que se haya pasado el ID
    if (empty($_POST['id'])) {
        $_SESSION['error'] = 'ID de mantenimiento no proporcionado.';
        header("Location: ../mantenimientos.php");
        exit;
    }

    $id_mantenimiento = $_POST['id'];
    
    // Validar campos requeridos
    if (empty($_POST['id_vehiculo']) || empty($_POST['id_usuario_mantenimiento']) || 
        empty($_POST['fecha_mantenimiento']) || empty($_POST['id_estado_mantenimiento'])) {
        $_SESSION['error'] = 'Por favor completa todos los campos obligatorios.';
        header("Location: ../update-mantenimientos.php?id=" . $id_mantenimiento);
        exit;
    }

    $Mantenimiento_class = new Mantenimiento();

    try {
        // Preparar datos del mantenimiento
        $datos = [
            'id_vehiculo' => $_POST['id_vehiculo'],
            'id_usuario_mantenimiento' => $_POST['id_usuario_mantenimiento'],
            'id_estado_mantenimiento' => $_POST['id_estado_mantenimiento'],
            'fecha_mantenimiento' => $_POST['fecha_mantenimiento'],
            'proximo_mantenimiento' => !empty($_POST['proximo_mantenimiento']) ? $_POST['proximo_mantenimiento'] : null,
            'costo_mano_obra' => !empty($_POST['costo_mano_obra']) ? $_POST['costo_mano_obra'] : 0.00,
            'costo_materiales' => !empty($_POST['costo_materiales']) ? $_POST['costo_materiales'] : 0.00,
            'observaciones' => $_POST['observaciones'] ?? ''
        ];

        // Actualizar el mantenimiento
        $actualizado = $Mantenimiento_class->actualizarMantenimiento($id_mantenimiento, $datos);

        if ($actualizado) {
            // Primero, eliminar todos los materiales existentes
            $Mantenimiento_class->eliminarMaterialesPorMantenimiento($id_mantenimiento);

            // Procesar materiales si existen
            if (isset($_POST['materiales']) && is_array($_POST['materiales'])) {
                foreach ($_POST['materiales'] as $material) {
                    // Solo agregar si tiene nombre y cantidad
                    if (!empty($material['nombre']) && !empty($material['cantidad'])) {
                        $datos_material = [
                            'id_mantenimiento' => $id_mantenimiento,
                            'nombre_material' => $material['nombre'],
                            'cantidad' => $material['cantidad'],
                            'unidad_medida' => $material['unidad'] ?? 'unidad',
                            'costo_unitario' => !empty($material['costo_unitario']) ? $material['costo_unitario'] : 0.00,
                            'observaciones' => ''
                        ];
                        
                        $Mantenimiento_class->agregarMaterial($datos_material);
                    }
                }
            }

            // Actualizar el costo total de materiales
            $Mantenimiento_class->recalcularCostoTotal($id_mantenimiento);

            $_SESSION['exito'] = 'Mantenimiento actualizado correctamente.';
            header("Location: ../mantenimiento-detalle.php?id=" . $id_mantenimiento);
            exit;
        } else {
            $_SESSION['error'] = 'Error al actualizar el mantenimiento.';
            header("Location: ../update-mantenimientos.php?id=" . $id_mantenimiento);
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header("Location: ../update-mantenimientos.php?id=" . $id_mantenimiento);
        exit;
    }

} else {
    $_SESSION['error'] = 'Acceso no válido.';
    header("Location: ../mantenimientos.php");
    exit;
}
?>
