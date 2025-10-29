# Guía de Configuración y Prueba del Sistema de Login

## ✅ Correcciones Realizadas

### 1. **Clase Usuario (includes/Class-usuario.php)**
Se corrigieron TODOS los métodos que usaban `bindParam()` incorrectamente:

- ✅ `verificarUsuarioEmpresa()` - Corregido
- ✅ `crearUsuario()` - Corregido
- ✅ `obtenerUsuarioPorId()` - Corregido
- ✅ `actualizarUsuario()` - Corregido
- ✅ `eliminarUsuario()` - Corregido
- ✅ `validarCredenciales()` - Corregido (crítico para login)
- ✅ `obtenerUsuarioPorEmail()` - Corregido

### 2. **Clase Mantenimiento (includes/Class-mantenimientos.php)**
Se corrigieron los métodos con el mismo problema:

- ✅ `crearMantenimiento()` - Corregido
- ✅ `agregarMaterial()` - Corregido
- ✅ `actualizarMantenimiento()` - Corregido
- ✅ `actualizarMaterial()` - Corregido

### 3. **Login Page (login.php)**
- ✅ Agregados mensajes de error/éxito
- ✅ Session start al inicio del archivo
- ✅ Alertas Bootstrap para feedback visual

### 4. **Archivo de Validación (action/valide-usuario.php)**
- ✅ Orden correcto: `session_start()` antes de `require_once`
- ✅ Manejo de excepciones mejorado

### 5. **Index Page (index.php)**
- ✅ Verificación de sesión sin restricción de rol por defecto
- ✅ Comentarios para habilitar restricción de roles si se necesita

## 🚀 Pasos para Probar el Login

### Paso 1: Crear Usuario Administrador

**Opción A - Usando SQL (Recomendado):**

1. Abre phpMyAdmin o MySQL Workbench
2. Ejecuta el archivo: `includes/database/insert-admin-user.sql`

**Opción B - Ejecutar este SQL manualmente:**

```sql
USE sysmaintweb_db;

-- Crear empresa si no existe
INSERT INTO empresas (id, nombre, direccion, estado)
VALUES (1, 'Empresa de Prueba', 'Dirección de prueba', 1)
ON DUPLICATE KEY UPDATE nombre = nombre;

-- Crear rol si no existe
INSERT INTO roles (nombre, descripcion, estado)
VALUES ('admin', 'Administrador del sistema', 1)
ON DUPLICATE KEY UPDATE nombre = nombre;

-- Crear usuario admin
INSERT INTO usuarios (id_empresa, code_cc, first_name, last_name, email, password, phone, direccion, rol, estado)
VALUES (
    1,
    '123456789',
    'Admin',
    'Sistema',
    'admin@sysmaint.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    '3001234567',
    'Dirección administrativa',
    'admin',
    1
);
```

### Paso 2: Probar con el Script de Diagnóstico

1. Abre en el navegador: `http://localhost/sysmaintweb/test-login.php`
2. Este script te mostrará:
   - ✅ Estado de la conexión a la base de datos
   - 📋 Lista de usuarios existentes
   - 🔐 Formulario para probar credenciales
   - ℹ️ Información del sistema PHP/PDO

### Paso 3: Probar el Login Real

1. Abre: `http://localhost/sysmaintweb/login.php`
2. Ingresa las credenciales:
   - **Email:** `admin@sysmaint.com`
   - **Password:** `admin123`
3. Click en "Entrar"
4. Deberías ser redirigido a `index.php`

## 🔧 Solución de Problemas

### Problema: "Credenciales incorrectas"

**Causa:** El usuario no existe o la contraseña no coincide

**Solución:**
1. Ejecuta `test-login.php` para verificar usuarios en BD
2. Si no hay usuarios, ejecuta el SQL del Paso 1
3. Verifica que el email esté correcto (sin espacios)

### Problema: "Error al procesar el login"

**Causa:** Error en la base de datos (bindParam, conexión, etc.)

**Solución:**
1. Verifica la conexión en `includes/settings-db.php`
2. Asegúrate de que todas las tablas existan
3. Revisa los logs de PHP en XAMPP

### Problema: Redirección infinita (loop)

**Causa:** La sesión no se guarda correctamente

**Solución:**
1. Verifica que `session_start()` esté al inicio de cada archivo
2. Comprueba permisos de escritura en la carpeta de sesiones de PHP
3. En `php.ini`, verifica `session.save_path`

### Problema: "No tienes permisos"

**Causa:** Verificación de rol muy restrictiva

**Solución:**
El `index.php` ahora permite todos los roles por defecto. Si quieres restringir:
```php
// Descomentar en index.php:
if (!$Usuario_class->verificarPermisos('admin')) {
    $_SESSION['error'] = 'No tienes permisos';
    header("Location: login.php");
    exit;
}
```

## 📊 Verificación de la Base de Datos

### Verificar Estructura de Tablas

```sql
-- Ver estructura de usuarios
DESCRIBE usuarios;

-- Ver estructura de roles
DESCRIBE roles;

-- Ver estructura de empresas
DESCRIBE empresas;

-- Contar usuarios activos
SELECT COUNT(*) as total_usuarios FROM usuarios WHERE estado = 1;

-- Ver todos los usuarios con información completa
SELECT u.*, r.nombre as rol_nombre, e.nombre as empresa_nombre
FROM usuarios u
LEFT JOIN roles r ON u.rol = r.nombre
LEFT JOIN empresas e ON u.id_empresa = e.id
WHERE u.estado = 1;
```

## 🔐 Información de Seguridad

### Password Hashing
- Se usa `password_hash()` con `PASSWORD_DEFAULT` (bcrypt)
- Se verifica con `password_verify()`
- Nunca se almacenan contraseñas en texto plano

### Variables de Sesión Almacenadas
Cuando el login es exitoso, se guardan:
- `$_SESSION['user_id']` - ID del usuario
- `$_SESSION['user_email']` - Email
- `$_SESSION['user_first_name']` - Nombre
- `$_SESSION['user_last_name']` - Apellido
- `$_SESSION['user_rol']` - Rol del usuario
- `$_SESSION['user_rol_nombre']` - Nombre completo del rol
- `$_SESSION['user_empresa_id']` - ID de la empresa
- `$_SESSION['user_empresa_nombre']` - Nombre de la empresa
- `$_SESSION['user_token']` - Token de seguridad

## 📝 Usuarios de Prueba Adicionales

Si quieres crear más usuarios de prueba:

```sql
-- Técnico de mantenimiento
INSERT INTO usuarios (id_empresa, code_cc, first_name, last_name, email, password, phone, direccion, rol, estado)
VALUES (1, '987654321', 'Juan', 'Pérez', 'tecnico@sysmaint.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3009876543', 'Dirección técnico', 'mantenimiento', 1);

-- Conductor
INSERT INTO usuarios (id_empresa, code_cc, first_name, last_name, email, password, phone, direccion, rol, estado)
VALUES (1, '456789123', 'María', 'González', 'conductor@sysmaint.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '3001112233', 'Dirección conductor', 'conductor', 1);
```

**Todos usan la misma contraseña:** `admin123`

## ✨ Características del Sistema de Login

1. ✅ Validación de credenciales con password hashing
2. ✅ Verificación de estado del usuario (solo usuarios activos)
3. ✅ Almacenamiento completo de información en sesión
4. ✅ Redirección basada en roles
5. ✅ Opción "Recuérdame" con cookies (opcional)
6. ✅ Mensajes de error descriptivos
7. ✅ Token de seguridad en sesión
8. ✅ Protección contra usuarios inactivos

## 🎯 Próximos Pasos

Una vez que el login funcione:

1. ✅ Probar registro de mantenimientos (ya corregido)
2. ✅ Probar creación de usuarios (ya corregido)
3. ✅ Probar actualización de vehículos
4. ✅ Probar carga de imágenes
5. ✅ Probar visualización del carrusel
6. ✅ Implementar logout
7. ✅ Implementar recuperación de contraseña (opcional)

## 📞 Soporte

Si encuentras algún problema:

1. Revisa `test-login.php` primero
2. Verifica la consola del navegador (F12) para errores JavaScript
3. Revisa los logs de PHP en XAMPP
4. Asegúrate de que Apache y MySQL estén corriendo
5. Verifica la configuración de la base de datos en `settings-db.php`
