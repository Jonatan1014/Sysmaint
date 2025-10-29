-- Script para crear un usuario administrador de prueba
-- Ejecuta esto en phpMyAdmin o MySQL Workbench

USE sysmaintweb_db;

-- 1. Verificar si existe la empresa (si no existe, crearla)
INSERT INTO empresas (id, nombre, direccion, estado)
VALUES (1, 'Empresa de Prueba', 'Dirección de prueba', 1)
ON DUPLICATE KEY UPDATE nombre = nombre;

-- 2. Verificar si existe el rol (si no existe, crearlo)
INSERT INTO roles (nombre, descripcion, estado)
VALUES ('admin', 'Administrador del sistema', 1)
ON DUPLICATE KEY UPDATE nombre = nombre;

-- 3. Crear usuario administrador de prueba
-- Password: admin123
INSERT INTO usuarios (id_empresa, code_cc, first_name, last_name, email, password, phone, direccion, rol, estado)
VALUES (
    1,
    '123456789',
    'Admin',
    'Sistema',
    'admin@sysmaint.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    '3001234567',
    'Dirección administrativa',
    'admin',
    1
)
ON DUPLICATE KEY UPDATE
    first_name = 'Admin',
    last_name = 'Sistema',
    estado = 1;

-- 4. Verificar el usuario creado
SELECT u.*, r.nombre as rol_nombre, e.nombre as empresa_nombre
FROM usuarios u
LEFT JOIN roles r ON u.rol = r.nombre
LEFT JOIN empresas e ON u.id_empresa = e.id
WHERE u.email = 'admin@sysmaint.com';

-- Credenciales para login:
-- Email: admin@sysmaint.com
-- Password: admin123
