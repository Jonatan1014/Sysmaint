<?php
// test-login.php - Script de prueba para verificar el sistema de login
require_once 'includes/Class-usuario.php';

echo "<h1>Test del Sistema de Login</h1>";
echo "<hr>";

// 1. Verificar conexión a base de datos
echo "<h2>1. Verificando conexión a base de datos...</h2>";
try {
    $Usuario_class = new Usuario();
    echo "✅ Conexión exitosa<br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    exit;
}

// 2. Listar usuarios en la base de datos
echo "<h2>2. Usuarios en la base de datos:</h2>";
try {
    $usuarios = $Usuario_class->obtenerUsuarios();
    if (count($usuarios) > 0) {
        echo "✅ Se encontraron " . count($usuarios) . " usuario(s)<br><br>";
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Email</th><th>Nombre</th><th>Rol</th><th>Estado</th></tr>";
        foreach ($usuarios as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . $user['email'] . "</td>";
            echo "<td>" . $user['first_name'] . " " . $user['last_name'] . "</td>";
            echo "<td>" . $user['rol'] . "</td>";
            echo "<td>" . ($user['estado'] == 1 ? 'Activo' : 'Inactivo') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No hay usuarios en la base de datos<br>";
        echo "<p><strong>SOLUCIÓN:</strong> Necesitas crear al menos un usuario. Puedes hacerlo con el siguiente SQL:</p>";
        echo "<pre>
INSERT INTO usuarios (id_empresa, code_cc, first_name, last_name, email, password, phone, direccion, rol, estado)
VALUES (1, '123456', 'Admin', 'Sistema', 'admin@sysmaint.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "', '1234567890', 'Dirección', 'admin', 1);
</pre>";
    }
} catch (Exception $e) {
    echo "❌ Error al obtener usuarios: " . $e->getMessage() . "<br>";
}

// 3. Probar validación de credenciales
echo "<h2>3. Probar validación de credenciales:</h2>";
echo "<form method='POST'>";
echo "<label>Email: <input type='email' name='test_email' value='admin@sysmaint.com' required></label><br><br>";
echo "<label>Password: <input type='password' name='test_password' value='admin123' required></label><br><br>";
echo "<button type='submit' name='test_login'>Probar Login</button>";
echo "</form>";

if (isset($_POST['test_login'])) {
    $email = $_POST['test_email'];
    $password = $_POST['test_password'];
    
    echo "<h3>Resultado de la prueba:</h3>";
    echo "<strong>Email ingresado:</strong> " . htmlspecialchars($email) . "<br>";
    echo "<strong>Password ingresado:</strong> " . str_repeat('*', strlen($password)) . "<br><br>";
    
    try {
        $usuario = $Usuario_class->validarCredenciales($email, $password);
        
        if ($usuario) {
            echo "✅ <strong style='color:green'>LOGIN EXITOSO</strong><br><br>";
            echo "<pre>";
            print_r($usuario);
            echo "</pre>";
        } else {
            echo "❌ <strong style='color:red'>CREDENCIALES INCORRECTAS</strong><br>";
            echo "<p>Posibles razones:</p>";
            echo "<ul>";
            echo "<li>El email no existe en la base de datos</li>";
            echo "<li>La contraseña es incorrecta</li>";
            echo "<li>El usuario está inactivo (estado = 0)</li>";
            echo "</ul>";
        }
    } catch (Exception $e) {
        echo "❌ Error al validar credenciales: " . $e->getMessage() . "<br>";
    }
}

echo "<hr>";
echo "<h2>4. Información del sistema:</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "PDO disponible: " . (extension_loaded('pdo') ? '✅ Sí' : '❌ No') . "<br>";
echo "PDO MySQL disponible: " . (extension_loaded('pdo_mysql') ? '✅ Sí' : '❌ No') . "<br>";

?>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    table { border-collapse: collapse; margin: 20px 0; }
    th { background: #333; color: white; }
    pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
</style>
