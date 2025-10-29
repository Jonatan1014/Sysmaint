<?php
// action/update-usuarios.php
require_once '../includes/Class-usuario.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['rol'])) {
        
        $id = $_POST['id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $rol = $_POST['rol'];
        $estado = isset($_POST['estado']) && $_POST['estado'] === 'on' ? 1 : 0;
        $password = $_POST['password'] ?? ''; // Puede estar vacío si no se quiere cambiar
        
        $Usuario_class = new Usuario();
        
        $datos = [
            'code_cc' => $_POST['code_cc'] ?? '',
            'name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'phone' => $phone,
            'direccion' => $direccion,
            'rol' => $rol,
            'estado' => $estado
        ];

        // Solo agregar password si se proporcionó una nueva
        if (!empty($password)) {
            $datos['password'] = $password;
        }
        
        try {
            $resultado = $Usuario_class->actualizarUsuario($id, $datos);

            if ($resultado) {
                $_SESSION['exito'] = 'Usuario actualizado correctamente';
                header("Location: ../admin.php");
                exit;
            } else {
                $_SESSION['error'] = 'Error al actualizar el usuario.';
                header("Location: ../update-usuarios.php?id=" . $id);
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
            header("Location: ../update-usuarios.php?id=" . $id);
            exit;
        }
    } else {
        $_SESSION['error'] = 'Por favor, completa todos los campos obligatorios.';
        header("Location: ../update-usuarios.php?id=" . $_POST['id']);
        exit;
    }
} else {
    $_SESSION['error'] = 'Método no permitido';
    header("Location: ../admin.php");
    exit;
}
?>