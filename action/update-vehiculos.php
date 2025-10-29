<?php
// action/update-vehiculos.php
session_start();
require_once '../includes/Class-vehiculos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    // Validar que se haya pasado el ID
    if (empty($_POST['id'])) {
        $_SESSION['error'] = 'ID de vehículo no proporcionado.';
        header("Location: ../vehiculos.php");
        exit;
    }

    $id_vehiculo = $_POST['id'];
    $id_empresa = $_POST['id_empresa'] ?? $_SESSION['user_empresa_id'] ?? null;
    
    // Validar campos requeridos
    if (empty($_POST['placa']) || empty($_POST['id_categoria']) || 
        empty($_POST['color']) || !isset($_POST['estado'])) {
        $_SESSION['error'] = 'Por favor completa todos los campos obligatorios.';
        header("Location: ../update-vehiculos.php?id=" . $id_vehiculo);
        exit;
    }

    $Vehiculo_class = new Vehiculo();

    try {
        // Verificar que el vehículo existe y pertenece a la empresa
        $vehiculo_actual = $Vehiculo_class->obtenerVehiculoPorId($id_vehiculo);
        
        if (!$vehiculo_actual) {
            $_SESSION['error'] = 'Vehículo no encontrado.';
            header("Location: ../vehiculos.php");
            exit;
        }

        if ($vehiculo_actual['id_empresa'] != $id_empresa) {
            $_SESSION['error'] = 'No tienes permiso para editar este vehículo.';
            header("Location: ../vehiculos.php");
            exit;
        }

        // Verificar que la placa no esté duplicada (excepto para este vehículo)
        if ($Vehiculo_class->verificarPlaca($_POST['placa'], $id_empresa, $id_vehiculo)) {
            $_SESSION['error'] = 'Ya existe otro vehículo con esta placa en tu empresa.';
            header("Location: ../update-vehiculos.php?id=" . $id_vehiculo);
            exit;
        }

        // Procesar nuevas imágenes si se cargaron
        $imagenes_actuales = json_decode($vehiculo_actual['imagenes'] ?? '[]', true);
        if (!is_array($imagenes_actuales)) {
            $imagenes_actuales = [];
        }
        
        $imagenes_nuevas = [];
        if (!empty($_FILES['nuevas_imagenes']['name'][0])) {
            $upload_dir = '../assets/images/equipos/';
            
            // Crear directorio si no existe
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Obtener la placa limpia para nombres de archivo
            $placa_limpia = str_replace(['-', ' '], '', strtoupper(trim($_POST['placa'])));
            
            // Calcular el número de la siguiente imagen basado en las existentes
            $numero_actual = count($imagenes_actuales);
            
            $total_imagenes = count($_FILES['nuevas_imagenes']['name']);
            
            for ($i = 0; $i < $total_imagenes; $i++) {
                if ($_FILES['nuevas_imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['nuevas_imagenes']['tmp_name'][$i];
                    $original_name = $_FILES['nuevas_imagenes']['name'][$i];
                    
                    // Obtener extensión del archivo
                    $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                    
                    // Validar que sea una imagen
                    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (!in_array($extension, $extensiones_permitidas)) {
                        continue; // Saltar archivos que no sean imágenes
                    }
                    
                    // Crear nombre de archivo: placa_numero.extension
                    $numero_imagen = $numero_actual + $i + 1;
                    $nuevo_nombre = strtolower($placa_limpia) . '_' . $numero_imagen . '.' . $extension;
                    $ruta_destino = $upload_dir . $nuevo_nombre;
                    
                    // Mover archivo
                    if (move_uploaded_file($tmp_name, $ruta_destino)) {
                        $imagenes_nuevas[] = 'assets/images/equipos/' . $nuevo_nombre;
                    }
                }
            }
        }
        
        // Combinar imágenes actuales con las nuevas
        $todas_imagenes = array_merge($imagenes_actuales, $imagenes_nuevas);

        // Preparar datos del vehículo
        $datos = [
            'placa' => strtoupper(trim($_POST['placa'])),
            'color' => $_POST['color'],
            'id_categoria' => $_POST['id_categoria'],
            'id_conductor' => !empty($_POST['id_conductor']) ? $_POST['id_conductor'] : null,
            'descripcion' => $_POST['descripcion'] ?? '',
            'estado' => $_POST['estado'],
            'imagenes' => !empty($todas_imagenes) ? json_encode($todas_imagenes, JSON_UNESCAPED_SLASHES) : $vehiculo_actual['imagenes']
        ];

        // Actualizar el vehículo
        $actualizado = $Vehiculo_class->actualizarVehiculo($id_vehiculo, $datos);

        if ($actualizado) {
            $mensaje = 'Vehículo actualizado correctamente';
            if (count($imagenes_nuevas) > 0) {
                $mensaje .= '. Se agregaron ' . count($imagenes_nuevas) . ' nueva(s) imagen(es)';
            }
            $_SESSION['exito'] = $mensaje;
            header("Location: ../vehiculo-detalle.php?id=" . $id_vehiculo);
            exit;
        } else {
            $_SESSION['error'] = 'Error al actualizar el vehículo.';
            header("Location: ../update-vehiculos.php?id=" . $id_vehiculo);
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header("Location: ../update-vehiculos.php?id=" . $id_vehiculo);
        exit;
    }

} else {
    $_SESSION['error'] = 'Acceso no válido.';
    header("Location: ../vehiculos.php");
    exit;
}
?>