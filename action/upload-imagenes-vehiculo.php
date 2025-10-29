<?php
// action/upload-imagenes-vehiculo.php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagenes'])) {
    $id_vehiculo = $_POST['id_vehiculo'];
    
    if (empty($id_vehiculo)) {
        $_SESSION['error'] = 'ID de vehículo no proporcionado.';
        header("Location: ../update-vehiculos.php?id=" . $id_vehiculo);
        exit;
    }

    $imagenes = [];
    $target_dir = "../uploads/vehiculos/";
    
    // Crear directorio si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    foreach ($_FILES['imagenes']['name'] as $key => $name) {
        if ($_FILES['imagenes']['error'][$key] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['imagenes']['tmp_name'][$key];
            $file_name = basename($name);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $new_filename = $id_vehiculo . '_' . time() . '_' . $key . '.' . $file_ext;
            $target_file = $target_dir . $new_filename;

            // Validar tipo de archivo
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($file_ext, $allowed_types)) {
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $imagenes[] = 'uploads/vehiculos/' . $new_filename;
                }
            }
        }
    }

    if (!empty($imagenes)) {
        // Aquí deberías actualizar la base de datos con las rutas de las imágenes
        // Esto requiere modificar la tabla de vehículos para incluir un campo JSON para imágenes
        $_SESSION['exito'] = count($imagenes) . ' imagen(es) subida(s) correctamente';
    } else {
        $_SESSION['error'] = 'No se pudieron subir las imágenes.';
    }
}

header("Location: ../update-vehiculos.php?id=" . $id_vehiculo);
exit;
?>