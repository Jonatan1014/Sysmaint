<?php
// includes/Class-vehiculos.php
require_once 'conn-db.php';
require_once 'Class-usuario.php'; // Para obtener información del conductor

class Vehiculo {
    private $conn;
    private $table_name = "vehiculos";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear nuevo vehículo
    public function crearVehiculo($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_empresa, placa, color, id_categoria, id_conductor, descripcion, estado, imagenes) 
                  VALUES (:id_empresa, :placa, :color, :id_categoria, :id_conductor, :descripcion, :estado, :imagenes)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_empresa', $datos['id_empresa']);
        $stmt->bindParam(':placa', $datos['placa']);
        $stmt->bindParam(':color', $datos['color']);
        $stmt->bindParam(':id_categoria', $datos['id_categoria']);
        $stmt->bindParam(':id_conductor', $datos['id_conductor']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':estado', $datos['estado']);
        
        // Permitir que imagenes sea null si no se proporcionó
        $imagenes = $datos['imagenes'] ?? null;
        $stmt->bindParam(':imagenes', $imagenes);

        if($stmt->execute()) {
            return $this->conn->lastInsertId(); // Devuelve el ID del vehículo recién creado
        }
        return false; // Error
    }

    // Actualizar vehículo
    public function actualizarVehiculo($id, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET placa = :placa, color = :color, id_categoria = :id_categoria, 
                      id_conductor = :id_conductor, descripcion = :descripcion, 
                      estado = :estado, imagenes = :imagenes 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':placa', $datos['placa']);
        $stmt->bindParam(':color', $datos['color']);
        $stmt->bindParam(':id_categoria', $datos['id_categoria']);
        $stmt->bindParam(':id_conductor', $datos['id_conductor']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':estado', $datos['estado']);
        $stmt->bindParam(':imagenes', $datos['imagenes']);

        return $stmt->execute();
    }

    // Eliminar vehículo (cambiar estado a 0)
    public function eliminarVehiculo($id) {
        $query = "UPDATE " . $this->table_name . " SET estado = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Obtener todos los vehículos con información adicional
    public function obtenerVehiculos($id_empresa = null) {
        $query = "SELECT v.*, cv.nombre as categoria_nombre, cv.descripcion as categoria_descripcion,
                         u.first_name as conductor_nombre, u.last_name as conductor_apellido,
                         u.email as conductor_email
                  FROM " . $this->table_name . " v
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  LEFT JOIN usuarios u ON v.id_conductor = u.id
                  WHERE v.estado = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        $query .= " ORDER BY v.created_at DESC";

        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener vehículo por ID
    public function obtenerVehiculoPorId($id) {
        $query = "SELECT v.*, cv.nombre as categoria_nombre, cv.descripcion as categoria_descripcion,
                         u.first_name as conductor_nombre, u.last_name as conductor_apellido,
                         u.email as conductor_email
                  FROM " . $this->table_name . " v
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  LEFT JOIN usuarios u ON v.id_conductor = u.id
                  WHERE v.id = ? AND v.estado = 1 LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si la placa ya existe
    public function verificarPlaca($placa, $id_empresa, $id_vehiculo = null) {
        $query = "SELECT id FROM " . $this->table_name . " 
                  WHERE id_empresa = ? AND placa = ? AND estado = 1";
        
        if ($id_vehiculo) {
            $query .= " AND id != ?";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_empresa);
        $stmt->bindParam(2, $placa);
        
        if ($id_vehiculo) {
            $stmt->bindParam(3, $id_vehiculo);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Obtener categorías de vehículos
    public function obtenerCategorias() {
        $query = "SELECT * FROM categorias_vehiculos WHERE estado = 1 ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener conductores de una empresa
    public function obtenerConductores($id_empresa) {
        $query = "SELECT id, first_name, last_name, email 
                  FROM usuarios 
                  WHERE id_empresa = ? AND rol = 'conductor' AND estado = 1 
                  ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id_empresa);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Contar vehículos por empresa
    public function contarVehiculos($id_empresa = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE estado = 1";
        if ($id_empresa) {
            $query .= " AND id_empresa = :id_empresa";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Obtener vehículos por categoría
    public function obtenerVehiculosPorCategoria($id_categoria, $id_empresa = null) {
        $query = "SELECT v.*, cv.nombre as categoria_nombre,
                        u.first_name as conductor_nombre, u.last_name as conductor_apellido
                FROM " . $this->table_name . " v
                LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                LEFT JOIN usuarios u ON v.id_conductor = u.id
                WHERE v.id_categoria = :id_categoria AND v.estado = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_categoria', $id_categoria);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas de vehículos
    public function obtenerEstadisticas($id_empresa = null) {
        $estadisticas = [
            'total_vehiculos' => 0,
            'total_categorias' => 0,
            'con_conductor' => 0,
            'sin_conductor' => 0,
            'por_categoria' => [],
            'por_estado' => [
                'activos' => 0,
                'inactivos' => 0
            ]
        ];

        // Total de vehículos
        $estadisticas['total_vehiculos'] = $this->contarVehiculos($id_empresa);

        // Total de categorías
        $estadisticas['total_categorias'] = count($this->obtenerCategorias());

        // Vehículos con y sin conductor
        $query = "SELECT 
                    SUM(CASE WHEN id_conductor IS NOT NULL THEN 1 ELSE 0 END) as con_conductor,
                    SUM(CASE WHEN id_conductor IS NULL THEN 1 ELSE 0 END) as sin_conductor
                  FROM " . $this->table_name . " WHERE estado = 1";
        
        if ($id_empresa) {
            $query .= " AND id_empresa = :id_empresa";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['con_conductor'] = $result['con_conductor'] ?? 0;
        $estadisticas['sin_conductor'] = $result['sin_conductor'] ?? 0;

        // Vehículos por categoría
        $query = "SELECT cv.nombre, cv.descripcion, COUNT(v.id) as cantidad
                  FROM categorias_vehiculos cv
                  LEFT JOIN " . $this->table_name . " v ON v.id_categoria = cv.id AND v.estado = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " WHERE cv.estado = 1 GROUP BY cv.id, cv.nombre, cv.descripcion ORDER BY cantidad DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $estadisticas['por_categoria'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Vehículos activos e inactivos
        $query = "SELECT 
                    SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) as inactivos
                  FROM " . $this->table_name;
        
        if ($id_empresa) {
            $query .= " WHERE id_empresa = :id_empresa";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['por_estado']['activos'] = $result['activos'] ?? 0;
        $estadisticas['por_estado']['inactivos'] = $result['inactivos'] ?? 0;

        return $estadisticas;
    }

    // Validar acceso de usuario a un vehículo
    public function validarAccesoVehiculo($id_vehiculo, $id_empresa) {
        $query = "SELECT COUNT(*) as tiene_acceso
                  FROM " . $this->table_name . "
                  WHERE id = :id_vehiculo AND id_empresa = :id_empresa";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->bindParam(':id_empresa', $id_empresa);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['tiene_acceso'] > 0;
    }

    // Cambiar estado de un vehículo
    public function cambiarEstado($id_vehiculo, $estado) {
        $query = "UPDATE " . $this->table_name . " 
                  SET estado = :estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_vehiculo);
        $stmt->bindParam(':estado', $estado);
        
        return $stmt->execute();
    }

    // Cambiar conductor de un vehículo
    public function cambiarConductor($id_vehiculo, $id_conductor) {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_conductor = :id_conductor 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_vehiculo);
        $stmt->bindParam(':id_conductor', $id_conductor);
        
        return $stmt->execute();
    }
}
?>