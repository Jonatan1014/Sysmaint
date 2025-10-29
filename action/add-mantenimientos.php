<?php
// action/add-mantenimientos.php
session_start();
require_once '../includes/Class-mantenimientos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    // Validar campos requeridos
    if (empty($_POST['id_vehiculo']) || empty($_POST['id_usuario_mantenimiento']) || 
        empty($_POST['fecha_mantenimiento']) || empty($_POST['id_estado_mantenimiento'])) {
        $_SESSION['error'] = 'Por favor completa todos los campos obligatorios.';
        header("Location: ../add-mantenimientos.php");
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

        // Crear el mantenimiento
        $id_mantenimiento = $Mantenimiento_class->crearMantenimiento($datos);

        if ($id_mantenimiento) {
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

            // Procesar imágenes si existen
            if (isset($_FILES['imagenes']) && $_FILES['imagenes']['error'][0] != UPLOAD_ERR_NO_FILE) {
                $imagenes = [];
                $upload_dir = '../assets/images/mantenimientos/';
                
                // Crear directorio si no existe
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['imagenes']['error'][$key] == UPLOAD_ERR_OK) {
                        $extension = pathinfo($_FILES['imagenes']['name'][$key], PATHINFO_EXTENSION);
                        $nombre_archivo = 'mant_' . $id_mantenimiento . '_' . time() . '_' . $key . '.' . $extension;
                        $ruta_destino = $upload_dir . $nombre_archivo;
                        
                        if (move_uploaded_file($tmp_name, $ruta_destino)) {
                            $imagenes[] = 'assets/images/mantenimientos/' . $nombre_archivo;
                        }
                    }
                }

                // Actualizar el mantenimiento con las rutas de las imágenes
                if (!empty($imagenes)) {
                    $imagenes_json = json_encode($imagenes);
                    $query = "UPDATE mantenimientos SET imagenes = :imagenes WHERE id = :id";
                    // Aquí necesitarías acceso directo a la conexión o crear un método en la clase
                }
            }

            $_SESSION['exito'] = 'Mantenimiento registrado correctamente.';
            header("Location: ../mantenimiento-detalle.php?id=" . $id_mantenimiento);
            exit;
        } else {
            $_SESSION['error'] = 'Error al registrar el mantenimiento.';
            header("Location: ../add-mantenimientos.php");
            exit;
        }

    } catch (Exception $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header("Location: ../add-mantenimientos.php");
        exit;
    }

} else {
    $_SESSION['error'] = 'Acceso no válido.';
    header("Location: ../add-mantenimientos.php");
    exit;
}
?>
