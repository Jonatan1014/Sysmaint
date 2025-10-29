-- ============================================
-- SCRIPT DE DATOS DE EJEMPLO - SYSMAINTWEB_DB
-- ============================================

USE sysmaintweb_db;

-- ============================================
-- 1. ROLES
-- ============================================
INSERT INTO roles (nombre, descripcion, estado) VALUES
('Administrador', 'Acceso total al sistema', 1),
('Supervisor', 'Gestión de mantenimientos y usuarios', 1),
('Mecánico', 'Ejecuta y registra mantenimientos', 1),
('Conductor', 'Operador de vehículos', 1),
('Visualizador', 'Solo lectura de información', 1);

-- ============================================
-- 2. EMPRESA
-- ============================================
INSERT INTO empresas (nombre, direccion, servicios, vehiculos_horarios, estado) VALUES
(
    'TransLogística Colombia S.A.S',
    'Calle 26 #68-80, Bogotá D.C., Colombia',
    'Transporte de carga, logística urbana, distribución nacional, mantenimiento preventivo y correctivo de flota',
    'Lunes a Viernes: 6:00 AM - 10:00 PM | Sábados: 8:00 AM - 6:00 PM | Domingos: Servicio de emergencias',
    1
);

-- ============================================
-- 3. CATEGORÍAS DE VEHÍCULOS
-- ============================================
INSERT INTO categorias_vehiculos (nombre, descripcion, estado) VALUES
('Camión de carga', 'Vehículos pesados para transporte de mercancías', 1),
('Camioneta', 'Vehículos medianos para carga ligera y pasajeros', 1),
('Automóvil', 'Vehículos livianos para transporte de personal', 1),
('Motocicleta', 'Vehículos de dos ruedas para mensajería rápida', 1),
('Montacargas', 'Equipos para manejo de carga en bodegas', 1);

-- ============================================
-- 4. USUARIOS
-- ============================================
INSERT INTO usuarios (id_empresa, code_cc, first_name, last_name, email, password, imagen_perfil, phone, direccion, rol, estado) VALUES
-- Administradores
(1, '1012345678', 'Jonatan David', 'Cantillo Torres', 'iadevelopment404@gmail.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi',"assets/images/uploads/user/avatar-1.jpg", '3001234567', 'Cra 15 #45-23, Bogotá', 'Administrador', 1),

-- Supervisores
(1, '1023456789', 'María', 'Gómez', 'maria.gomez@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-2.jpg', '3101234567', 'Calle 80 #12-34, Bogotá', 'Supervisor', 1),

-- Mecánicos
(1, '1034567890', 'Juan', 'Pérez', 'juan.perez@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-3.jpg', '3201234567', 'Av. Boyacá #45-67, Bogotá', 'Mecánico', 1),
(1, '1045678901', 'Pedro', 'Martínez', 'pedro.martinez@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-4.jpg', '3301234567', 'Calle 13 #89-12, Bogotá', 'Mecánico', 1),
(1, '1056789012', 'Diego', 'López', 'diego.lopez@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-5.jpg', '3401234567', 'Cra 50 #23-45, Bogotá', 'Mecánico', 1),

-- Conductores
(1, '1067890123', 'Luis', 'Ramírez', 'luis.ramirez@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-6.jpg', '3501234567', 'Calle 40 #67-89, Bogotá', 'Conductor', 1),
(1, '1078901234', 'José', 'Torres', 'jose.torres@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-7.jpg', '3601234567', 'Av. Caracas #34-56, Bogotá', 'Conductor', 1),
(1, '1089012345', 'Miguel', 'Díaz', 'miguel.diaz@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-8.jpg', '3701234567', 'Cra 7 #78-90, Bogotá', 'Conductor', 1),
(1, '1090123456', 'Fernando', 'Vargas', 'fernando.vargas@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-9.jpg', '3801234567', 'Calle 100 #12-34, Bogotá', 'Conductor', 1),
(1, '1101234567', 'Roberto', 'Castro', 'roberto.castro@translogistica.com', '$2y$10$4ch8QBRsDGBW119RzOTpM.t2.tjaTYJVYc7hOcWKWzn17SUUlRMJi', 'assets/images/uploads/user/avatar-10.jpg', '3901234567', 'Cra 30 #45-67, Bogotá', 'Conductor', 1);

-- ============================================
-- 5. VEHÍCULOS
-- ============================================
INSERT INTO vehiculos (id_empresa, placa, color, id_categoria, id_conductor, imagenes, descripcion, estado) VALUES
-- Camiones
(1, 'ABC123', 'Blanco', 1, 6, '["assets/images/uploads/vehiculos/abc123_1.jpg", "assets/images/uploads/vehiculos/abc123_2.jpg"]', 'Camión NPR Chevrolet 2020 - Capacidad 5 toneladas', 1),
(1, 'DEF456', 'Rojo', 1, 7, '["assets/images/uploads/vehiculos/def456_1.jpg"]', 'Camión FTR Isuzu 2019 - Capacidad 8 toneladas', 1),
(1, 'GHI789', 'Azul', 1, 8, '["assets/images/uploads/vehiculos/ghi789_1.jpg"]', 'Camión Hino 2021 - Capacidad 6 toneladas', 1),

-- Camionetas
(1, 'JKL012', 'Gris', 2, 9, '["assets/images/uploads/vehiculos/jkl012_1.jpg"]', 'Toyota Hilux 2022 4x4 - Carga mixta', 1),
(1, 'MNO345', 'Negro', 2, 10, '["assets/images/uploads/vehiculos/mno345_1.jpg"]', 'Chevrolet D-MAX 2021 - Transporte de personal y herramientas', 1),

-- Automóviles
(1, 'PQR678', 'Plata', 3, NULL, '["assets/images/uploads/vehiculos/pqr678_1.jpg"]', 'Chevrolet Spark 2023 - Vehículo administrativo', 1),
(1, 'STU901', 'Blanco', 3, NULL, '["assets/images/uploads/vehiculos/stu901_1.jpg"]', 'Renault Logan 2022 - Pool de vehículos', 1),

-- Motocicletas
(1, 'VWX234', 'Negro', 4, NULL, '["assets/images/uploads/vehiculos/vwx234_1.jpg"]', 'Honda CB125 2023 - Mensajería urbana', 1),
(1, 'YZA567', 'Rojo', 4, NULL, '["assets/images/uploads/vehiculos/yza567_1.jpg"]', 'Yamaha XTZ125 2022 - Domicilios', 1),

-- Montacargas
(1, 'MTC001', 'Amarillo', 5, NULL, '["assets/images/uploads/vehiculos/mtc001_1.jpg"]', 'Montacargas Toyota 3 toneladas - Bodega principal', 1);

-- ============================================
-- 6. MANTENIMIENTOS
-- ============================================
INSERT INTO mantenimientos (id_vehiculo, id_usuario_mantenimiento, id_estado_mantenimiento, fecha_mantenimiento, proximo_mantenimiento, costo_mano_obra, costo_materiales, observaciones, imagenes) VALUES
-- Mantenimiento completado
(1, 3, 4, '2025-09-15', '2026-03-15', 250000.00, 450000.00, 'Cambio de aceite, filtros y revisión general. Vehículo en óptimas condiciones.', '["assets/images/uploads/mantenimientos/mant_001_1.jpg", "assets/images/uploads/mantenimientos/mant_001_2.jpg"]'),

(2, 4, 4, '2025-08-20', '2026-02-20', 180000.00, 320000.00, 'Revisión de frenos, cambio de pastillas delanteras y traseras.', '["assets/images/uploads/mantenimientos/mant_002_1.jpg"]'),

(3, 3, 4, '2025-10-01', '2026-04-01', 300000.00, 550000.00, 'Mantenimiento preventivo de 20,000 km. Cambio de correa de distribución.', '["assets/images/uploads/mantenimientos/mant_003_1.jpg"]'),

-- En reparación
(4, 5, 3, '2025-10-25', '2026-04-25', 200000.00, 0.00, 'Reparación de sistema de suspensión. Pendiente cotización de amortiguadores.', '["assets/images/uploads/mantenimientos/mant_004_1.jpg"]'),

(5, 4, 3, '2025-10-27', '2026-04-27', 150000.00, 280000.00, 'Cambio de embrague en proceso. Se requiere ajuste de cilindro maestro.', NULL),

-- En revisión
(1, 3, 2, '2025-10-28', '2026-01-28', 0.00, 0.00, 'Revisión diagnóstica por ruido en motor. En proceso de evaluación.', NULL),

(6, 5, 2, '2025-10-29', '2026-04-29', 0.00, 0.00, 'Inspección pre-operacional programada. Verificación de sistema eléctrico.', NULL),

-- Pendiente
(7, 4, 1, '2025-11-05', '2026-05-05', 0.00, 0.00, 'Mantenimiento preventivo programado de 10,000 km.', NULL),

(8, 3, 1, '2025-11-10', '2026-05-10', 0.00, 0.00, 'Revisión general y cambio de aceite programado.', NULL),

(9, 5, 1, '2025-11-15', '2026-05-15', 0.00, 0.00, 'Inspección técnico-mecánica pendiente.', NULL);

-- ============================================
-- 7. MATERIALES DE MANTENIMIENTO
-- ============================================
INSERT INTO materiales_mantenimiento (id_mantenimiento, nombre_material, cantidad, unidad_medida, costo_unitario, observaciones) VALUES
-- Materiales mantenimiento ID 1 (ABC123)
(1, 'Aceite motor 15W40', 6.000, 'litros', 25000.00, 'Aceite sintético premium'),
(1, 'Filtro de aceite', 1.000, 'unidad', 35000.00, 'Filtro original OEM'),
(1, 'Filtro de aire', 1.000, 'unidad', 45000.00, 'Filtro de alta eficiencia'),
(1, 'Filtro de combustible', 1.000, 'unidad', 65000.00, 'Filtro separador de agua'),
(1, 'Líquido refrigerante', 4.000, 'litros', 18000.00, 'Refrigerante larga duración'),
(1, 'Mano de obra adicional', 1.000, 'servicio', 120000.00, 'Lavado de motor y ajustes'),

-- Materiales mantenimiento ID 2 (DEF456)
(2, 'Pastillas de freno delanteras', 1.000, 'juego', 180000.00, 'Pastillas cerámicas'),
(2, 'Pastillas de freno traseras', 1.000, 'juego', 140000.00, 'Pastillas semi-metálicas'),

-- Materiales mantenimiento ID 3 (GHI789)
(3, 'Correa de distribución', 1.000, 'unidad', 280000.00, 'Kit completo con rodillos'),
(3, 'Bomba de agua', 1.000, 'unidad', 150000.00, 'Bomba original'),
(3, 'Aceite motor 10W30', 7.000, 'litros', 28000.00, 'Aceite full sintético'),
(3, 'Empaquetadura múltiple', 1.000, 'kit', 80000.00, 'Juego completo de empaques'),

-- Materiales mantenimiento ID 5 (MNO345)
(5, 'Kit de embrague', 1.000, 'kit', 450000.00, 'Disco, prensa y collarín'),
(5, 'Cilindro auxiliar de embrague', 1.000, 'unidad', 120000.00, 'Cilindro hidráulico'),
(5, 'Líquido de frenos DOT4', 1.000, 'litros', 25000.00, 'Líquido sintético');

