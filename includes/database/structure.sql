-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS sysmaintweb_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sysmaintweb_db;

-- Tabla de roles de usuario
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de empresas
CREATE TABLE empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    direccion TEXT,
    servicios TEXT,
    vehiculos_horarios TEXT,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de categorías de vehículos
CREATE TABLE categorias_vehiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    code_cc VARCHAR(20) NOT NULL, -- Cédula o código de identificación
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    direccion TEXT,
    rol VARCHAR(50) NOT NULL, -- Relación con la tabla roles
    imagen_perfil VARCHAR(255),
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empresa) REFERENCES empresas(id),
    FOREIGN KEY (rol) REFERENCES roles(nombre)
);

-- Tabla de vehículos
CREATE TABLE vehiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    placa VARCHAR(20) NOT NULL UNIQUE,
    color VARCHAR(50),
    id_categoria INT NOT NULL,
    id_conductor INT, -- Relación con usuario conductor
    imagenes JSON, -- Almacenar rutas de imágenes en formato JSON
    descripcion TEXT,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_empresa) REFERENCES empresas(id),
    FOREIGN KEY (id_categoria) REFERENCES categorias_vehiculos(id),
    FOREIGN KEY (id_conductor) REFERENCES usuarios(id)
);

-- Tabla de estados de mantenimiento
CREATE TABLE estados_mantenimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion TEXT,
    color_hex VARCHAR(7) DEFAULT '#CCCCCC',
    orden SMALLINT DEFAULT 0,
    estado TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar estados iniciales (opcional, pero recomendado)
INSERT INTO estados_mantenimiento (nombre, descripcion, color_hex, orden) VALUES
('Pendiente', 'Mantenimiento programado pero no iniciado', '#FFA500', 1),
('En revisión', 'Diagnóstico o inspección en curso', '#1E90FF', 2),
('En reparación', 'Trabajo activo en el vehículo', '#FF4500', 3),
('Completado', 'Mantenimiento finalizado', '#32CD32', 4),
('Cancelado', 'Mantenimiento anulado', '#808080', 5);

-- Tabla de mantenimientos (actualizada)
DROP TABLE IF EXISTS materiales_mantenimiento; -- Por dependencia
DROP TABLE IF EXISTS mantenimientos;

CREATE TABLE mantenimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_vehiculo INT NOT NULL,
    id_usuario_mantenimiento INT NOT NULL,
    id_estado_mantenimiento INT NOT NULL DEFAULT 1,
    fecha_mantenimiento DATE NOT NULL,
    proximo_mantenimiento DATE,
    costo_mano_obra DECIMAL(10, 2) DEFAULT 0.00,
    costo_materiales DECIMAL(10, 2) DEFAULT 0.00,
    costo_total DECIMAL(10, 2) GENERATED ALWAYS AS (costo_mano_obra + costo_materiales) STORED,
    observaciones TEXT,
    imagenes JSON,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario_mantenimiento) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (id_estado_mantenimiento) REFERENCES estados_mantenimiento(id) ON DELETE RESTRICT
);

-- Tabla de materiales (sin cambios, pero aseguramos que se cree después)
CREATE TABLE materiales_mantenimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_mantenimiento INT NOT NULL,
    nombre_material VARCHAR(255) NOT NULL,
    cantidad DECIMAL(10, 3) NOT NULL DEFAULT 1.000,
    unidad_medida VARCHAR(50) DEFAULT 'unidad',
    costo_unitario DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    costo_total DECIMAL(10, 2) GENERATED ALWAYS AS (cantidad * costo_unitario) STORED,
    observaciones TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_mantenimiento) REFERENCES mantenimientos(id) ON DELETE CASCADE
);
