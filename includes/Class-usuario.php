<?php
// includes/Class-usuario.php
require_once 'conn-db.php';

class Usuario {
    private $conn;
    private $table_name = "usuarios";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Verificar si el usuario ya existe en la empresa
    public function verificarUsuarioEmpresa($idEmpresa, $code_cc, $email) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE id_empresa = ? AND (code_cc = ? OR email = ?) LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Asignar variables para bindParam
        $id_emp = $idEmpresa;
        $cc = $code_cc;
        $em = $email;
        
        $stmt->bindParam(1, $id_emp);
        $stmt->bindParam(2, $cc);
        $stmt->bindParam(3, $em);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Crear nuevo usuario
    public function crearUsuario($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_empresa, code_cc, first_name, last_name, email, password, phone, direccion, rol, estado) 
                  VALUES (:id_empresa, :code_cc, :first_name, :last_name, :email, :password, :phone, :direccion, :rol, :estado)";

        $stmt = $this->conn->prepare($query);

        // Asignar variables para bindParam
        $id_empresa = $datos['idEmpresa'];
        $code_cc = $datos['code_cc'];
        $first_name = $datos['name'];
        $last_name = $datos['last_name'];
        $email = $datos['email'];
        $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
        $phone = $datos['phone'];
        $direccion = $datos['direccion'];
        $rol = $datos['rol'];
        $estado = $datos['estado'];

        $stmt->bindParam(':id_empresa', $id_empresa);
        $stmt->bindParam(':code_cc', $code_cc);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':estado', $estado);

        if($stmt->execute()) {
            return false; // Éxito
        }
        return true; // Error
    }

    // Obtener todos los usuarios
    public function obtenerUsuarios() {
        $query = "SELECT u.*, r.nombre as rol_nombre, e.nombre as empresa_nombre 
                  FROM " . $this->table_name . " u
                  LEFT JOIN roles r ON u.rol = r.nombre
                  LEFT JOIN empresas e ON u.id_empresa = e.id
                  WHERE u.estado = 1
                  ORDER BY u.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener usuario por ID
    public function obtenerUsuarioPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        
        // Asignar variable para bindParam
        $user_id = $id;
        
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualizar usuario
    public function actualizarUsuario($id, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET code_cc = :code_cc, first_name = :first_name, last_name = :last_name, 
                      email = :email, phone = :phone, direccion = :direccion, rol = :rol, 
                      estado = :estado" . 
                  (isset($datos['password']) && !empty($datos['password']) ? ", password = :password" : "") . 
                  " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Asignar variables para bindParam
        $user_id = $id;
        $code_cc = $datos['code_cc'];
        $first_name = $datos['name'];
        $last_name = $datos['last_name'];
        $email = $datos['email'];
        $phone = $datos['phone'];
        $direccion = $datos['direccion'];
        $rol = $datos['rol'];
        $estado = $datos['estado'];

        $stmt->bindParam(':id', $user_id);
        $stmt->bindParam(':code_cc', $code_cc);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':estado', $estado);

        if (isset($datos['password']) && !empty($datos['password'])) {
            $password_hash = password_hash($datos['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $password_hash);
        }

        return $stmt->execute();
    }

    // Eliminar usuario (cambiar estado a 0)
    public function eliminarUsuario($id) {
        $query = "UPDATE " . $this->table_name . " SET estado = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        
        // Asignar variable para bindParam
        $user_id = $id;
        
        $stmt->bindParam(1, $user_id);
        return $stmt->execute();
    }


    // Añadir esta función a la clase Usuario (en includes/Class-usuario.php)

    // Validar credenciales para login - Versión mejorada
    public function validarCredenciales($email, $password) {
        $query = "SELECT u.*, r.nombre as rol_nombre, e.nombre as empresa_nombre 
                FROM " . $this->table_name . " u
                LEFT JOIN roles r ON u.rol = r.nombre
                LEFT JOIN empresas e ON u.id_empresa = e.id
                WHERE u.email = ? AND u.estado = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Asignar variable para bindParam
        $user_email = $email;
        
        $stmt->bindParam(1, $user_email);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($password, $usuario['password'])) {
            return $usuario;
        }
        return false;
    }

    // Obtener información completa del usuario por email
    public function obtenerUsuarioPorEmail($email) {
        $query = "SELECT u.*, r.nombre as rol_nombre, e.nombre as empresa_nombre 
                FROM " . $this->table_name . " u
                LEFT JOIN roles r ON u.rol = r.nombre
                LEFT JOIN empresas e ON u.id_empresa = e.id
                WHERE u.email = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        
        // Asignar variable para bindParam
        $user_email = $email;
        
        $stmt->bindParam(1, $user_email);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // En includes/Class-usuario.php o en un nuevo archivo includes/auth.php

    public function usuarioLogueado() {
        return isset($_SESSION['user_id']) && isset($_SESSION['user_token']);
    }

    public function verificarPermisos($rol_requerido) {
        if (!$this->usuarioLogueado()) {
            return false;
        }
        
        $rol_usuario = $_SESSION['user_rol'];
        return $rol_usuario === $rol_requerido || $rol_usuario === 'admin' || $rol_usuario === 'root';
    }

    // Obtener roles disponibles
    public function obtenerRoles() {
        $query = "SELECT nombre, descripcion FROM roles WHERE estado = 1 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar imagen de perfil
    public function actualizarImagenPerfil($id_usuario, $imagen_url) {
        $query = "UPDATE usuarios SET imagen_perfil = :imagen_url WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':imagen_url', $imagen_url);
        $stmt->bindParam(':id', $id_usuario);
        
        return $stmt->execute();
    }

    // Verificar si un email ya existe (excepto para un usuario específico)
    public function verificarEmailExistente($email, $id_usuario_excluir = null) {
        if ($id_usuario_excluir) {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email AND id != :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $id_usuario_excluir);
        } else {
            $query = "SELECT COUNT(*) as total FROM usuarios WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] > 0;
    }

    // Actualizar datos personales del perfil
    public function actualizarDatosPerfil($id_usuario, $datos) {
        $query = "UPDATE usuarios SET 
                  first_name = :first_name,
                  last_name = :last_name,
                  email = :email,
                  phone = :phone,
                  direccion = :direccion
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $first_name = $datos['first_name'];
        $last_name = $datos['last_name'];
        $email = $datos['email'];
        $phone = $datos['phone'];
        $direccion = $datos['direccion'];
        
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':id', $id_usuario);
        
        return $stmt->execute();
    }

    // Actualizar contraseña
    public function actualizarPassword($id_usuario, $password_hash) {
        $query = "UPDATE usuarios SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $id_usuario);
        
        return $stmt->execute();
    }


}
?>