<?php
// action/add-vehiculos.php
require_once '../includes/Class-vehiculos.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['placa']) && !empty($_POST['id_categoria']) && !empty($_POST['id_empresa'])) {
        
        $placa = strtoupper(trim($_POST['placa'])); // Convertir a mayúsculas
        $placa_limpia = str_replace(['-', ' '], '', $placa); // Remover guiones y espacios para el nombre del archivo
        $color = $_POST['color'] ?? '';
        $id_categoria = $_POST['id_categoria'];
        $id_conductor = $_POST['id_conductor'] ?? null;
        $descripcion = $_POST['descripcion'] ?? '';
        $id_empresa = $_POST['id_empresa'];
        $estado = isset($_POST['estado']) && $_POST['estado'] === 'on' ? 1 : 0;
       
        $Vehiculo_class = new Vehiculo();
        
        try {
            // Verificar si la placa ya existe
            if ($Vehiculo_class->verificarPlaca($placa, $id_empresa)) {
                $_SESSION['error'] = 'La placa ya está registrada. Intenta con otra.';
                header("Location: ../add-vehiculos.php");
                exit;
            }
            
            // Procesar imágenes si se cargaron
            $imagenes_guardadas = [];
            if (!empty($_FILES['imagenes']['name'][0])) {
                $upload_dir = '../assets/images/equipos/';
                
                // Crear directorio si no existe
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $total_imagenes = count($_FILES['imagenes']['name']);
                
                for ($i = 0; $i < $total_imagenes; $i++) {
                    if ($_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['imagenes']['tmp_name'][$i];
                        $original_name = $_FILES['imagenes']['name'][$i];
                        
                        // Obtener extensión del archivo
                        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
                        
                        // Validar que sea una imagen
                        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        if (!in_array($extension, $extensiones_permitidas)) {
                            continue; // Saltar archivos que no sean imágenes
                        }
                        
                        // Crear nombre de archivo: placa_numero.extension
                        $numero_imagen = $i + 1;
                        $nuevo_nombre = strtolower($placa_limpia) . '_' . $numero_imagen . '.' . $extension;
                        $ruta_destino = $upload_dir . $nuevo_nombre;
                        
                        // Mover archivo
                        if (move_uploaded_file($tmp_name, $ruta_destino)) {
                            $imagenes_guardadas[] = 'assets/images/equipos/' . $nuevo_nombre;
                        }
                    }
                }
            }
            
            $datos = [
                'id_empresa' => $id_empresa,
                'placa' => $placa,
                'color' => $color,
                'id_categoria' => $id_categoria,
                'id_conductor' => $id_conductor,
                'descripcion' => $descripcion,
                'estado' => $estado,
                'imagenes' => !empty($imagenes_guardadas) ? json_encode($imagenes_guardadas, JSON_UNESCAPED_SLASHES) : null
            ];
            
            // Crear el vehículo
            $id_nuevo_vehiculo = $Vehiculo_class->crearVehiculo($datos);

            if ($id_nuevo_vehiculo) {
                $mensaje = 'Vehículo registrado correctamente';
                if (count($imagenes_guardadas) > 0) {
                    $mensaje .= ' con ' . count($imagenes_guardadas) . ' imagen(es)';
                }
                $_SESSION['exito'] = $mensaje;
                header("Location: ../vehiculo-detalle.php?id=" . $id_nuevo_vehiculo);
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar el vehículo.';
                header("Location: ../add-vehiculos.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            header("Location: ../add-vehiculos.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios.';
        header("Location: ../add-vehiculos.php");
        exit;
    }
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../add-vehiculos.php");
    exit;
}
?>