<?php
// includes/Class-mantenimientos.php
require_once 'conn-db.php';

class Mantenimiento {
    private $conn;
    private $table_name = "mantenimientos";
    private $table_materiales = "materiales_mantenimiento";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Crear nuevo mantenimiento
    public function crearMantenimiento($datos) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_vehiculo, id_usuario_mantenimiento, id_estado_mantenimiento, 
                   fecha_mantenimiento, proximo_mantenimiento, costo_mano_obra, 
                   costo_materiales, observaciones, activo) 
                  VALUES (:id_vehiculo, :id_usuario_mantenimiento, :id_estado_mantenimiento, 
                          :fecha_mantenimiento, :proximo_mantenimiento, :costo_mano_obra,
                          :costo_materiales, :observaciones, 1)";

        $stmt = $this->conn->prepare($query);

        // Asignar variables para bindParam
        $id_vehiculo = $datos['id_vehiculo'];
        $id_usuario = $datos['id_usuario_mantenimiento'];
        $id_estado = $datos['id_estado_mantenimiento'];
        $fecha_mant = $datos['fecha_mantenimiento'];
        $proximo_mant = $datos['proximo_mantenimiento'] ?? null;
        $costo_mano = $datos['costo_mano_obra'] ?? 0.00;
        $costo_mat = $datos['costo_materiales'] ?? 0.00;
        $obs = $datos['observaciones'] ?? '';

        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->bindParam(':id_usuario_mantenimiento', $id_usuario);
        $stmt->bindParam(':id_estado_mantenimiento', $id_estado);
        $stmt->bindParam(':fecha_mantenimiento', $fecha_mant);
        $stmt->bindParam(':proximo_mantenimiento', $proximo_mant);
        $stmt->bindParam(':costo_mano_obra', $costo_mano);
        $stmt->bindParam(':costo_materiales', $costo_mat);
        $stmt->bindParam(':observaciones', $obs);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Actualizar mantenimiento
    public function actualizarMantenimiento($id, $datos) {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_estado_mantenimiento = :id_estado_mantenimiento,
                      fecha_mantenimiento = :fecha_mantenimiento, 
                      proximo_mantenimiento = :proximo_mantenimiento,
                      costo_mano_obra = :costo_mano_obra,
                      costo_materiales = :costo_materiales,
                      observaciones = :observaciones
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Asignar variables para bindParam
        $id_mant = $id;
        $id_estado = $datos['id_estado_mantenimiento'];
        $fecha_mant = $datos['fecha_mantenimiento'];
        $proximo_mant = $datos['proximo_mantenimiento'] ?? null;
        $costo_mano = $datos['costo_mano_obra'] ?? 0.00;
        $costo_mat = $datos['costo_materiales'] ?? 0.00;
        $obs = $datos['observaciones'] ?? '';

        $stmt->bindParam(':id', $id_mant);
        $stmt->bindParam(':id_estado_mantenimiento', $id_estado);
        $stmt->bindParam(':fecha_mantenimiento', $fecha_mant);
        $stmt->bindParam(':proximo_mantenimiento', $proximo_mant);
        $stmt->bindParam(':costo_mano_obra', $costo_mano);
        $stmt->bindParam(':costo_materiales', $costo_mat);
        $stmt->bindParam(':observaciones', $obs);

        return $stmt->execute();
    }

    // Cambiar estado del mantenimiento
    public function cambiarEstado($id_mantenimiento, $id_estado) {
        $query = "UPDATE " . $this->table_name . " 
                  SET id_estado_mantenimiento = :id_estado 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id_mantenimiento);
        $stmt->bindParam(':id_estado', $id_estado);
        
        return $stmt->execute();
    }

    // Eliminar mantenimiento (cambiar activo a 0)
    public function eliminarMantenimiento($id) {
        $query = "UPDATE " . $this->table_name . " SET activo = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }

    // Obtener todos los mantenimientos
    public function obtenerMantenimientos($id_empresa = null) {
        $query = "SELECT m.*, 
                         v.placa as vehiculo_placa, v.color as vehiculo_color,
                         cv.nombre as vehiculo_categoria,
                         u.first_name as tecnico_nombre, u.last_name as tecnico_apellido,
                         em.nombre as estado_nombre, em.color_hex as estado_color
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  INNER JOIN usuarios u ON m.id_usuario_mantenimiento = u.id
                  INNER JOIN estados_mantenimiento em ON m.id_estado_mantenimiento = em.id
                  WHERE m.activo = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " ORDER BY m.fecha_mantenimiento DESC";

        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener mantenimientos de un vehículo específico
    public function obtenerMantenimientosPorVehiculo($id_vehiculo) {
        $query = "SELECT m.*, 
                         u.first_name as tecnico_nombre, u.last_name as tecnico_apellido,
                         em.nombre as estado_nombre, em.color_hex as estado_color,
                         (SELECT COUNT(*) FROM " . $this->table_materiales . " 
                          WHERE id_mantenimiento = m.id) as total_materiales
                  FROM " . $this->table_name . " m
                  INNER JOIN usuarios u ON m.id_usuario_mantenimiento = u.id
                  INNER JOIN estados_mantenimiento em ON m.id_estado_mantenimiento = em.id
                  WHERE m.id_vehiculo = :id_vehiculo AND m.activo = 1
                  ORDER BY m.fecha_mantenimiento DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener mantenimiento por ID
    public function obtenerMantenimientoPorId($id) {
        $query = "SELECT m.*, 
                         v.placa as vehiculo_placa, v.color as vehiculo_color, v.descripcion as vehiculo_descripcion,
                         cv.nombre as vehiculo_categoria,
                         u.first_name as tecnico_nombre, u.last_name as tecnico_apellido, u.email as tecnico_email,
                         em.nombre as estado_nombre, em.color_hex as estado_color
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  INNER JOIN usuarios u ON m.id_usuario_mantenimiento = u.id
                  INNER JOIN estados_mantenimiento em ON m.id_estado_mantenimiento = em.id
                  WHERE m.id = :id AND m.activo = 1 LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener estados de mantenimiento
    public function obtenerEstadosMantenimiento() {
        $query = "SELECT * FROM estados_mantenimiento WHERE estado = 1 ORDER BY orden";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =================== MATERIALES ===================

    // Agregar material al mantenimiento
    public function agregarMaterial($datos) {
        $query = "INSERT INTO " . $this->table_materiales . " 
                  (id_mantenimiento, nombre_material, cantidad, unidad_medida, 
                   costo_unitario, observaciones) 
                  VALUES (:id_mantenimiento, :nombre_material, :cantidad, :unidad_medida, 
                          :costo_unitario, :observaciones)";

        $stmt = $this->conn->prepare($query);

        // Asignar variables para bindParam
        $id_mant = $datos['id_mantenimiento'];
        $nombre = $datos['nombre_material'];
        $cant = $datos['cantidad'];
        $unidad = $datos['unidad_medida'];
        $costo_unit = $datos['costo_unitario'];
        $obs = $datos['observaciones'] ?? '';

        $stmt->bindParam(':id_mantenimiento', $id_mant);
        $stmt->bindParam(':nombre_material', $nombre);
        $stmt->bindParam(':cantidad', $cant);
        $stmt->bindParam(':unidad_medida', $unidad);
        $stmt->bindParam(':costo_unitario', $costo_unit);
        $stmt->bindParam(':observaciones', $obs);

        if($stmt->execute()) {
            // Actualizar el costo total del mantenimiento
            $this->actualizarCostoTotal($datos['id_mantenimiento']);
            return $this->conn->lastInsertId();
        }
        return false;
    }

    // Obtener materiales de un mantenimiento
    public function obtenerMaterialesPorMantenimiento($id_mantenimiento) {
        $query = "SELECT * FROM " . $this->table_materiales . " 
                  WHERE id_mantenimiento = :id_mantenimiento 
                  ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_mantenimiento', $id_mantenimiento);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar material
    public function actualizarMaterial($id, $datos) {
        $query = "UPDATE " . $this->table_materiales . " 
                  SET nombre_material = :nombre_material,
                      cantidad = :cantidad,
                      unidad_medida = :unidad_medida,
                      costo_unitario = :costo_unitario,
                      observaciones = :observaciones
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        
        // Asignar variables para bindParam
        $id_mat = $id;
        $nombre = $datos['nombre_material'];
        $cant = $datos['cantidad'];
        $unidad = $datos['unidad_medida'];
        $costo_unit = $datos['costo_unitario'];
        $obs = $datos['observaciones'] ?? '';
        
        $stmt->bindParam(':id', $id_mat);
        $stmt->bindParam(':nombre_material', $nombre);
        $stmt->bindParam(':cantidad', $cant);
        $stmt->bindParam(':unidad_medida', $unidad);
        $stmt->bindParam(':costo_unitario', $costo_unit);
        $stmt->bindParam(':observaciones', $obs);

        if($stmt->execute()) {
            // Obtener el id_mantenimiento para actualizar el costo total
            $query_mant = "SELECT id_mantenimiento FROM " . $this->table_materiales . " WHERE id = ?";
            $stmt_mant = $this->conn->prepare($query_mant);
            $stmt_mant->bindParam(1, $id);
            $stmt_mant->execute();
            $material = $stmt_mant->fetch(PDO::FETCH_ASSOC);
            
            $this->actualizarCostoTotal($material['id_mantenimiento']);
            return true;
        }
        return false;
    }

    // Eliminar material
    public function eliminarMaterial($id) {
        // Obtener el id_mantenimiento antes de eliminar
        $query_mant = "SELECT id_mantenimiento FROM " . $this->table_materiales . " WHERE id = ?";
        $stmt_mant = $this->conn->prepare($query_mant);
        $stmt_mant->bindParam(1, $id);
        $stmt_mant->execute();
        $material = $stmt_mant->fetch(PDO::FETCH_ASSOC);

        $query = "DELETE FROM " . $this->table_materiales . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        if($stmt->execute()) {
            $this->actualizarCostoTotal($material['id_mantenimiento']);
            return true;
        }
        return false;
    }

    // Actualizar costo de materiales del mantenimiento basado en la suma de materiales
    private function actualizarCostoTotal($id_mantenimiento) {
        $query = "UPDATE " . $this->table_name . " 
                  SET costo_materiales = (
                      SELECT COALESCE(SUM(costo_total), 0) 
                      FROM " . $this->table_materiales . " 
                      WHERE id_mantenimiento = :id_mantenimiento
                  )
                  WHERE id = :id_mantenimiento";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_mantenimiento', $id_mantenimiento);
        return $stmt->execute();
    }

    // =================== ESTADÍSTICAS Y REPORTES ===================

    // Obtener costo total de mantenimientos por vehículo
    public function obtenerCostoTotalPorVehiculo($id_vehiculo) {
        $query = "SELECT COALESCE(SUM(costo_total), 0) as costo_total 
                  FROM " . $this->table_name . " 
                  WHERE id_vehiculo = :id_vehiculo AND activo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['costo_total'];
    }

    // Obtener próximos mantenimientos programados
    public function obtenerProximosMantenimientos($id_empresa = null, $dias = 30) {
        $fecha_limite = date('Y-m-d', strtotime("+$dias days"));
        
        $query = "SELECT m.*, 
                         v.placa as vehiculo_placa, v.color as vehiculo_color,
                         cv.nombre as vehiculo_categoria,
                         u.first_name as tecnico_nombre, u.last_name as tecnico_apellido
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  INNER JOIN usuarios u ON m.id_usuario_mantenimiento = u.id
                  WHERE m.activo = 1 
                  AND m.proximo_mantenimiento IS NOT NULL 
                  AND m.proximo_mantenimiento BETWEEN CURDATE() AND :fecha_limite";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " ORDER BY m.proximo_mantenimiento ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha_limite', $fecha_limite);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener mantenimientos por estado
    public function obtenerMantenimientosPorEstado($id_estado, $id_empresa = null) {
        $query = "SELECT m.*, 
                         v.placa as vehiculo_placa, v.color as vehiculo_color,
                         cv.nombre as vehiculo_categoria,
                         u.first_name as tecnico_nombre, u.last_last as tecnico_apellido
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  INNER JOIN usuarios u ON m.id_usuario_mantenimiento = u.id
                  WHERE m.activo = 1 AND m.id_estado_mantenimiento = :id_estado";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " ORDER BY m.fecha_mantenimiento DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_estado', $id_estado);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Contar mantenimientos totales
    public function contarMantenimientos($id_empresa = null) {
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  WHERE m.activo = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Obtener historial completo de mantenimiento de un vehículo
    public function obtenerHistorialVehiculo($id_vehiculo) {
        $query = "SELECT m.*, 
                         u.first_name as tecnico_nombre, u.last_name as tecnico_apellido,
                         em.nombre as estado_nombre, em.color_hex as estado_color,
                         (SELECT COUNT(*) FROM " . $this->table_materiales . " 
                          WHERE id_mantenimiento = m.id) as total_materiales
                  FROM " . $this->table_name . " m
                  INNER JOIN usuarios u ON m.id_usuario_mantenimiento = u.id
                  INNER JOIN estados_mantenimiento em ON m.id_estado_mantenimiento = em.id
                  WHERE m.id_vehiculo = :id_vehiculo AND m.activo = 1
                  ORDER BY m.fecha_mantenimiento DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener técnicos de una empresa
    public function obtenerTecnicos($id_empresa = null) {
        $query = "SELECT id, CONCAT(first_name, ' ', last_name) as nombre_completo, email
                  FROM usuarios
                  WHERE rol IN ('tecnico', 'admin') AND estado = 1";
        
        if ($id_empresa) {
            $query .= " AND id_empresa = :id_empresa";
        }
        
        $query .= " ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($query);
        
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Eliminar todos los materiales de un mantenimiento
    public function eliminarMaterialesPorMantenimiento($id_mantenimiento) {
        try {
            $query = "DELETE FROM " . $this->table_materiales . " WHERE id_mantenimiento = :id_mantenimiento";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id_mantenimiento', $id_mantenimiento);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al eliminar materiales: " . $e->getMessage());
            return false;
        }
    }

    // Hacer pública la función actualizarCostoTotal para poder llamarla externamente
    public function recalcularCostoTotal($id_mantenimiento) {
        return $this->actualizarCostoTotal($id_mantenimiento);
    }

    // Obtener estadísticas de mantenimientos
    public function obtenerEstadisticas($id_empresa = null) {
        $estadisticas = [
            'total_mantenimientos' => 0,
            'proximos_30_dias' => 0,
            'costo_total' => 0,
            'por_estado' => []
        ];

        // Total de mantenimientos
        $query = "SELECT COUNT(*) as total 
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  WHERE m.activo = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['total_mantenimientos'] = $result['total'];

        // Costo total
        $query = "SELECT COALESCE(SUM(m.costo_total), 0) as total 
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  WHERE m.activo = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['costo_total'] = $result['total'];

        // Próximos mantenimientos en 30 días
        $proximos = $this->obtenerProximosMantenimientos($id_empresa, 30);
        $estadisticas['proximos_30_dias'] = count($proximos);

        // Estadísticas por estado
        $query = "SELECT em.nombre, em.color_hex as color, COUNT(m.id) as cantidad
                  FROM estados_mantenimiento em
                  LEFT JOIN " . $this->table_name . " m ON m.id_estado_mantenimiento = em.id AND m.activo = 1
                  LEFT JOIN vehiculos v ON m.id_vehiculo = v.id";
        
        if ($id_empresa) {
            $query .= " WHERE v.id_empresa = :id_empresa OR m.id IS NULL";
        }
        
        $query .= " GROUP BY em.id, em.nombre, em.color_hex ORDER BY em.orden";
        
        $stmt = $this->conn->prepare($query);
        if ($id_empresa) {
            $stmt->bindParam(':id_empresa', $id_empresa);
        }
        $stmt->execute();
        $estadisticas['por_estado'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $estadisticas;
    }

    // Validar si un mantenimiento pertenece a una empresa
    public function validarAccesoMantenimiento($id_mantenimiento, $id_empresa) {
        $query = "SELECT COUNT(*) as tiene_acceso
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  WHERE m.id = :id_mantenimiento AND v.id_empresa = :id_empresa";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_mantenimiento', $id_mantenimiento);
        $stmt->bindParam(':id_empresa', $id_empresa);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['tiene_acceso'] > 0;
    }

    // Obtener materiales más comprados
    public function obtenerMaterialesMasComprados($id_empresa = null, $limite = 10) {
        $query = "SELECT mm.nombre_material, 
                         SUM(mm.cantidad) as cantidad_total,
                         SUM(mm.costo_total) as costo_total,
                         COUNT(DISTINCT mm.id_mantenimiento) as veces_usado,
                         mm.unidad_medida
                  FROM " . $this->table_materiales . " mm
                  INNER JOIN " . $this->table_name . " m ON mm.id_mantenimiento = m.id
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  WHERE m.activo = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " GROUP BY mm.nombre_material, mm.unidad_medida
                    ORDER BY cantidad_total DESC
                    LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        
        if ($id_empresa) {
            $id_emp = $id_empresa;
            $stmt->bindParam(':id_empresa', $id_emp);
        }
        
        $lim = $limite;
        $stmt->bindParam(':limite', $lim, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener costo de mantenimiento mensual
    public function obtenerCostoMensual($id_empresa = null, $meses = 12) {
        $query = "SELECT DATE_FORMAT(fecha_mantenimiento, '%Y-%m') as mes,
                         DATE_FORMAT(fecha_mantenimiento, '%M %Y') as mes_nombre,
                         COUNT(*) as cantidad_mantenimientos,
                         SUM(costo_mano_obra) as total_mano_obra,
                         SUM(costo_materiales) as total_materiales,
                         SUM(costo_total) as total_costo
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  WHERE m.activo = 1
                  AND m.fecha_mantenimiento >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " GROUP BY DATE_FORMAT(fecha_mantenimiento, '%Y-%m')
                    ORDER BY mes DESC";
        
        $stmt = $this->conn->prepare($query);
        
        $mes_param = $meses;
        $stmt->bindParam(':meses', $mes_param, PDO::PARAM_INT);
        
        if ($id_empresa) {
            $id_emp = $id_empresa;
            $stmt->bindParam(':id_empresa', $id_emp);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener vehículos con más mantenimientos
    public function obtenerVehiculosConMasMantenimientos($id_empresa = null, $limite = 10) {
        $query = "SELECT v.id, v.placa, v.color,
                         cv.nombre as categoria,
                         COUNT(m.id) as total_mantenimientos,
                         SUM(m.costo_total) as costo_total_acumulado,
                         AVG(m.costo_total) as costo_promedio,
                         MAX(m.fecha_mantenimiento) as ultimo_mantenimiento
                  FROM vehiculos v
                  LEFT JOIN " . $this->table_name . " m ON v.id = m.id_vehiculo AND m.activo = 1
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  WHERE v.estado = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " GROUP BY v.id, v.placa, v.color, cv.nombre
                    HAVING total_mantenimientos > 0
                    ORDER BY total_mantenimientos DESC
                    LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        
        if ($id_empresa) {
            $id_emp = $id_empresa;
            $stmt->bindParam(':id_empresa', $id_emp);
        }
        
        $lim = $limite;
        $stmt->bindParam(':limite', $lim, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener análisis de costos por vehículo
    public function obtenerCostosPorVehiculo($id_empresa = null) {
        $query = "SELECT v.id, v.placa, v.color,
                         cv.nombre as categoria,
                         COUNT(m.id) as total_mantenimientos,
                         SUM(m.costo_mano_obra) as total_mano_obra,
                         SUM(m.costo_materiales) as total_materiales,
                         SUM(m.costo_total) as costo_total
                  FROM vehiculos v
                  LEFT JOIN " . $this->table_name . " m ON v.id = m.id_vehiculo AND m.activo = 1
                  LEFT JOIN categorias_vehiculos cv ON v.id_categoria = cv.id
                  WHERE v.estado = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " GROUP BY v.id, v.placa, v.color, cv.nombre
                    ORDER BY costo_total DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($id_empresa) {
            $id_emp = $id_empresa;
            $stmt->bindParam(':id_empresa', $id_emp);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener razones de mantenimiento más comunes
    public function obtenerRazonesMantenimiento($id_empresa = null) {
        $query = "SELECT em.nombre as estado,
                         em.color_hex as color,
                         COUNT(m.id) as cantidad,
                         AVG(m.costo_total) as costo_promedio
                  FROM " . $this->table_name . " m
                  INNER JOIN vehiculos v ON m.id_vehiculo = v.id
                  INNER JOIN estados_mantenimiento em ON m.id_estado_mantenimiento = em.id
                  WHERE m.activo = 1";
        
        if ($id_empresa) {
            $query .= " AND v.id_empresa = :id_empresa";
        }
        
        $query .= " GROUP BY em.id, em.nombre, em.color_hex
                    ORDER BY cantidad DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($id_empresa) {
            $id_emp = $id_empresa;
            $stmt->bindParam(':id_empresa', $id_emp);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener dashboard completo
    public function obtenerDashboardCompleto($id_empresa = null) {
        return [
            'estadisticas_generales' => $this->obtenerEstadisticas($id_empresa),
            'materiales_mas_comprados' => $this->obtenerMaterialesMasComprados($id_empresa, 10),
            'costo_mensual' => $this->obtenerCostoMensual($id_empresa, 12),
            'vehiculos_mas_mantenimiento' => $this->obtenerVehiculosConMasMantenimientos($id_empresa, 10),
            'costos_por_vehiculo' => $this->obtenerCostosPorVehiculo($id_empresa),
            'razones_mantenimiento' => $this->obtenerRazonesMantenimiento($id_empresa),
            'proximos_mantenimientos' => $this->obtenerProximosMantenimientos($id_empresa, 30)
        ];
    }
}
?>

