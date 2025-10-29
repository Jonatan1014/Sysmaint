<?php
// action/add-usuarios.php
require_once '../includes/Class-usuario.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['rol'])) {
        
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $rol = $_POST['rol'];
        $id_empresa = $_POST['id_empresa'] ?? 1; // Ajusta según tu sistema
        $estado = isset($_POST['estado']) && $_POST['estado'] === 'on' ? 1 : 0;
       
        $Usuario_class = new Usuario();
        
        try {
            // Verificar si el correo ya está registrado
            if ($Usuario_class->verificarUsuarioEmpresa($id_empresa, $_POST['code_cc'] ?? '', $email)) {
                $_SESSION['error'] = 'El correo o la cédula ya está registrado. Intenta con otro.';
                header("Location: ../add-usuarios.php");
                exit;
            }
            
            $datos = [
                'idEmpresa' => $id_empresa,
                'code_cc' => $_POST['code_cc'] ?? '',
                'name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'direccion' => $direccion,
                'rol' => $rol,
                'estado' => $estado
            ];
            
            // Registrar el usuario
            $operar = $Usuario_class->crearUsuario($datos);

            if (!$operar) {
                $_SESSION['exito'] = 'Usuario registrado correctamente';
                header("Location: ../add-usuarios.php");
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar el usuario.';
                header("Location: ../add-usuarios.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            header("Location: ../add-usuarios.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios.';
        header("Location: ../add-usuarios.php");
        exit;
    }
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../add-usuarios.php");
    exit;
}
?>