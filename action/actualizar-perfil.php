<?php
session_start();
require_once '../includes/Class-usuario.php';

$Usuario_class = new Usuario();

// Verificar que el usuario esté logueado
if (!$Usuario_class->usuarioLogueado()) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ==================== SUBIR IMAGEN DE PERFIL ====================
if (isset($_FILES['imagen_perfil'])) {
    header('Content-Type: application/json');
    
    $file = $_FILES['imagen_perfil'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validar tipo de archivo
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y GIF'
        ]);
        exit;
    }
    
    // Validar tamaño
    if ($file['size'] > $max_size) {
        echo json_encode([
            'success' => false,
            'message' => 'El archivo es demasiado grande. Máximo 5MB'
        ]);
        exit;
    }
    
    // Crear directorio si no existe
    $upload_dir = '../assets/images/users/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generar nombre único para la imagen
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    // Obtener usuario actual para eliminar imagen anterior
    $usuario_actual = $Usuario_class->obtenerUsuarioPorId($user_id);
    
    // Subir archivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Actualizar ruta en base de datos
        $imagen_url = 'assets/images/users/' . $filename;
        
        try {
            $Usuario_class->actualizarImagenPerfil($user_id, $imagen_url);
            
            // Eliminar imagen anterior si existe y no es la default
            if (!empty($usuario_actual['imagen_perfil']) && 
                $usuario_actual['imagen_perfil'] !== 'assets/images/users/user-default.png' &&
                file_exists('../' . $usuario_actual['imagen_perfil'])) {
                unlink('../' . $usuario_actual['imagen_perfil']);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Foto de perfil actualizada correctamente',
                'imagen_url' => $imagen_url
            ]);
        } catch (Exception $e) {
            // Si falla la actualización, eliminar el archivo subido
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar la imagen: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al subir el archivo'
        ]);
    }
    exit;
}

// ==================== ACTUALIZAR DATOS PERSONALES ====================
if (isset($_POST['actualizar_datos'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $direccion = trim($_POST['direccion']);
    
    // Validaciones básicas
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $_SESSION['error'] = 'Por favor completa todos los campos obligatorios';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'El formato del email no es válido';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Verificar si el email ya existe (excepto el usuario actual)
    $email_existe = $Usuario_class->verificarEmailExistente($email, $user_id);
    if ($email_existe) {
        $_SESSION['error'] = 'El email ya está registrado por otro usuario';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Preparar datos para actualizar
    $datos = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'direccion' => $direccion
    ];
    
    try {
        $resultado = $Usuario_class->actualizarDatosPerfil($user_id, $datos);
        
        if ($resultado) {
            $_SESSION['exito'] = 'Datos personales actualizados correctamente';
        } else {
            $_SESSION['error'] = 'No se pudo actualizar la información';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
    }
    
    header("Location: ../mi-perfil.php");
    exit;
}

// ==================== CAMBIAR CONTRASEÑA ====================
if (isset($_POST['cambiar_password'])) {
    $password_actual = $_POST['password_actual'];
    $password_nueva = $_POST['password_nueva'];
    $password_confirmar = $_POST['password_confirmar'];
    
    // Validaciones
    if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
        $_SESSION['error'] = 'Por favor completa todos los campos de contraseña';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    if (strlen($password_nueva) < 8) {
        $_SESSION['error'] = 'La nueva contraseña debe tener al menos 8 caracteres';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    if ($password_nueva !== $password_confirmar) {
        $_SESSION['error'] = 'Las contraseñas no coinciden';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Obtener usuario actual
    $usuario = $Usuario_class->obtenerUsuarioPorId($user_id);
    
    // Verificar contraseña actual
    if (!password_verify($password_actual, $usuario['password'])) {
        $_SESSION['error'] = 'La contraseña actual es incorrecta';
        header("Location: ../mi-perfil.php");
        exit;
    }
    
    // Actualizar contraseña
    try {
        $password_hash = password_hash($password_nueva, PASSWORD_DEFAULT);
        $resultado = $Usuario_class->actualizarPassword($user_id, $password_hash);
        
        if ($resultado) {
            $_SESSION['exito'] = 'Contraseña actualizada correctamente';
        } else {
            $_SESSION['error'] = 'No se pudo actualizar la contraseña';
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error al cambiar la contraseña: ' . $e->getMessage();
    }
    
    header("Location: ../mi-perfil.php");
    exit;
}

// Si no hay acción válida, redirigir
header("Location: ../mi-perfil.php");
exit;
?>
