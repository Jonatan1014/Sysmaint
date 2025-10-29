<?php
session_start();
require_once 'includes/Class-usuario.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Obtener datos del usuario actual
$usuario = $Usuario_class->obtenerUsuarioPorId($_SESSION['user_id']);

if (!$usuario) {
    $_SESSION['error'] = 'No se pudo cargar la información del usuario';
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Mi Perfil | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto;
        }
        
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .profile-image-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: #727cf5;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .profile-image-upload:hover {
            background: #5a67d8;
        }
        
        .profile-image-upload input {
            display: none;
        }
        
        .info-item {
            padding: 12px 0;
            border-bottom: 1px solid #f1f3fa;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 4px;
        }
        
        .info-value {
            color: #313a46;
            font-size: 15px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Mensajes -->
                    <?php if (isset($_SESSION['exito'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Mi Perfil</h4>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Información del Perfil -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="profile-image-container mb-3">
                                            <img src="<?php echo !empty($usuario['imagen_perfil']) ? $usuario['imagen_perfil'] : 'assets/images/users/user-default.png'; ?>" 
                                                 alt="Foto de perfil" 
                                                 class="rounded-circle profile-image"
                                                 id="preview-imagen">
                                            
                                            <label class="profile-image-upload" for="imagen-perfil" title="Cambiar foto">
                                                <i class="mdi mdi-camera"></i>
                                                <input type="file" id="imagen-perfil" accept="image/*">
                                            </label>
                                        </div>
                                        
                                        <h4 class="mb-0"><?php echo htmlspecialchars($usuario['first_name'] . ' ' . $usuario['last_name']); ?></h4>
                                        <p class="text-muted">
                                            <span class="badge bg-primary"><?php echo htmlspecialchars($usuario['rol']); ?></span>
                                        </p>
                                    </div>

                                    <div class="mt-4">
                                        <h5 class="mb-3">Información Personal</h5>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="mdi mdi-email-outline me-1"></i> Email
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="mdi mdi-card-account-details-outline me-1"></i> Cédula
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($usuario['code_cc']); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="mdi mdi-phone-outline me-1"></i> Teléfono
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($usuario['phone'] ?? 'No registrado'); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="mdi mdi-map-marker-outline me-1"></i> Dirección
                                            </div>
                                            <div class="info-value"><?php echo htmlspecialchars($usuario['direccion'] ?? 'No registrada'); ?></div>
                                        </div>
                                        
                                        <div class="info-item">
                                            <div class="info-label">
                                                <i class="mdi mdi-calendar-outline me-1"></i> Miembro desde
                                            </div>
                                            <div class="info-value"><?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Editar Información -->
                        <div class="col-lg-8">
                            <!-- Datos Personales -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-account-edit text-primary me-1"></i>
                                        Editar Datos Personales
                                    </h4>
                                    
                                    <form method="POST" action="action/actualizar-perfil.php" id="form-datos">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="first_name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                                       value="<?php echo htmlspecialchars($usuario['first_name']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="last_name" class="form-label">Apellido <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                                       value="<?php echo htmlspecialchars($usuario['last_name']); ?>" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">Teléfono</label>
                                                <input type="text" class="form-control" id="phone" name="phone" 
                                                       value="<?php echo htmlspecialchars($usuario['phone'] ?? ''); ?>" 
                                                       placeholder="Ej: 3001234567">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="direccion" class="form-label">Dirección</label>
                                            <textarea class="form-control" id="direccion" name="direccion" rows="2" 
                                                      placeholder="Ingresa tu dirección completa"><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary" name="actualizar_datos">
                                                <i class="mdi mdi-content-save me-1"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Cambiar Contraseña -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-lock-reset text-warning me-1"></i>
                                        Cambiar Contraseña
                                    </h4>
                                    
                                    <form method="POST" action="action/actualizar-perfil.php" id="form-password">
                                        <div class="mb-3">
                                            <label for="password_actual" class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="password_actual" name="password_actual" 
                                                       placeholder="Ingresa tu contraseña actual" required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_actual')">
                                                    <i class="mdi mdi-eye" id="icon-password_actual"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="password_nueva" class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password_nueva" name="password_nueva" 
                                                           placeholder="Mínimo 8 caracteres" required minlength="8">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_nueva')">
                                                        <i class="mdi mdi-eye" id="icon-password_nueva"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">Mínimo 8 caracteres</small>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="password_confirmar" class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" 
                                                           placeholder="Repite la nueva contraseña" required minlength="8">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmar')">
                                                        <i class="mdi mdi-eye" id="icon-password_confirmar"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="mdi mdi-information me-1"></i>
                                            <strong>Recomendaciones:</strong>
                                            <ul class="mb-0 mt-2">
                                                <li>Usa al menos 8 caracteres</li>
                                                <li>Combina letras mayúsculas y minúsculas</li>
                                                <li>Incluye números y caracteres especiales</li>
                                            </ul>
                                        </div>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-warning" name="cambiar_password">
                                                <i class="mdi mdi-lock-reset me-1"></i> Cambiar Contraseña
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include("includes/footer.php"); ?>
        </div>
    </div>

    <?php include("includes/js.php"); ?>

    <script>
        // Preview de imagen antes de subir
        document.getElementById('imagen-perfil').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-imagen').src = e.target.result;
                }
                reader.readAsDataURL(file);
                
                // Subir imagen automáticamente
                subirImagen(file);
            }
        });

        function subirImagen(file) {
            const formData = new FormData();
            formData.append('imagen_perfil', file);

            fetch('action/actualizar-perfil.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show';
                    alert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('.row'));
                    
                    // Actualizar imagen en el header si existe
                    const headerImg = document.querySelector('.topnav .dropdown-toggle img');
                    if (headerImg) {
                        headerImg.src = data.imagen_url;
                    }
                } else {
                    alert('Error al subir la imagen: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            });
        }

        // Validar que las contraseñas coincidan
        document.getElementById('form-password').addEventListener('submit', function(e) {
            const nueva = document.getElementById('password_nueva').value;
            const confirmar = document.getElementById('password_confirmar').value;
            
            if (nueva !== confirmar) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor verifica.');
                return false;
            }
        });

        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById('icon-' + inputId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'mdi mdi-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'mdi mdi-eye';
            }
        }
    </script>

</body>
</html>
