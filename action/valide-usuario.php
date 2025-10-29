<?php
// action/valide-usuario.php
session_start();
require_once '../includes/Class-usuario.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['emailaddress']) && !empty($_POST['password'])) {
        
        $email = $_POST['emailaddress'];
        $password = $_POST['password'];
        $recuerdame = isset($_POST['recuerdame']) ? true : false;
        
        $Usuario_class = new Usuario();
        
        try {
            // Validar credenciales
            $usuario = $Usuario_class->validarCredenciales($email, $password);
            
            if ($usuario) {
                // Iniciar sesión con los datos del usuario
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['user_img'] = $usuario['imagen_perfil'];
                $_SESSION['user_imagen'] = $usuario['imagen_perfil']; // Alias para compatibilidad
                $_SESSION['user_code_cc'] = $usuario['code_cc'];
                $_SESSION['user_first_name'] = $usuario['first_name'];
                $_SESSION['user_last_name'] = $usuario['last_name'];
                $_SESSION['user_nombre'] = $usuario['first_name'] . ' ' . $usuario['last_name']; // Nombre completo
                $_SESSION['user_email'] = $usuario['email'];
                $_SESSION['user_phone'] = $usuario['phone'];
                $_SESSION['user_direccion'] = $usuario['direccion'];
                $_SESSION['user_rol'] = $usuario['rol'];
                $_SESSION['user_rol_nombre'] = $usuario['rol_nombre'];
                $_SESSION['user_empresa_id'] = $usuario['id_empresa'];
                $_SESSION['user_empresa_nombre'] = $usuario['empresa_nombre'];
                $_SESSION['user_estado'] = $usuario['estado'];
                
                // Opcional: Crear un token de sesión para mayor seguridad
                $_SESSION['user_token'] = bin2hex(random_bytes(32));
                
                // Manejar "Recuérdame"
                if ($recuerdame) {
                    // Crear cookie con token (opcional, para sesión persistente)
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (86400 * 30), "/"); // 30 días
                    // Aquí normalmente guardarías el token en la base de datos
                }
                
                // Redirigir según el rol del usuario
                switch ($usuario['rol']) {
                    case 'admin':
                    case 'root':
                        header("Location: ../index.php");
                        break;
                    case 'conductor':
                        header("Location: ../index.php"); // Ajusta según tu estructura
                        break;
                    case 'mantenimiento':
                        header("Location: ../index.php"); // Ajusta según tu estructura
                        break;
                    default:
                        header("Location: ../index.php"); // Página por defecto
                        break;
                }
                exit;
                
            } else {
                $_SESSION['error'] = 'Credenciales incorrectas. Por favor, verifica tu email y contraseña.';
                header("Location: ../login.php");
                exit;
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error al procesar el login: ' . $e->getMessage();
            header("Location: ../login.php");
            exit;
        }
        
    } else {
        $_SESSION['error'] = 'Por favor, ingresa tu email y contraseña.';
        header("Location: ../login.php");
        exit;
    }
    
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../login.php");
    exit;
}
?>