<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-vehiculos.php';

// Verificar si el usuario está logueado
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

// Verificar que se haya pasado un ID
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID de vehículo no proporcionado.';
    header('Location: vehiculos.php');
    exit;
}

$id_vehiculo = $_GET['id'];
$id_empresa = $_SESSION['user_empresa_id'] ?? null;

// Instanciar clase
$Vehiculo_class = new Vehiculo();

// Obtener datos del vehículo
$vehiculo = $Vehiculo_class->obtenerVehiculoPorId($id_vehiculo);

if (!$vehiculo) {
    $_SESSION['error'] = 'Vehículo no encontrado.';
    header('Location: vehiculos.php');
    exit;
}

// Verificar que el vehículo pertenezca a la empresa del usuario
if ($vehiculo['id_empresa'] != $id_empresa) {
    $_SESSION['error'] = 'No tienes permiso para editar este vehículo.';
    header('Location: vehiculos.php');
    exit;
}

// Obtener datos necesarios para el formulario
$categorias = $Vehiculo_class->obtenerCategorias();
$conductores = $Vehiculo_class->obtenerConductores($id_empresa);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Editar Vehículo | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="wrapper">
        <?php include("includes/header.php"); ?>
        <?php include("includes/sidebar.php"); ?>

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">

                    <!-- Mensajes -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Título -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <a href="vehiculo-detalle.php?id=<?php echo $id_vehiculo; ?>" class="btn btn-secondary btn-sm">
                                        <i class="mdi mdi-arrow-left me-1"></i> Cancelar
                                    </a>
                                </div>
                                <h4 class="page-title">Editar Vehículo: <?php echo htmlspecialchars($vehiculo['placa']); ?></h4>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="action/update-vehiculos.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $id_vehiculo; ?>">
                                        <input type="hidden" name="id_empresa" value="<?php echo $id_empresa; ?>">

                                        <div class="row">
                                            <!-- Columna izquierda -->
                                            <div class="col-lg-6">
                                                <h4 class="header-title mb-3">Información Básica</h4>

                                                <div class="mb-3">
                                                    <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="placa" name="placa" 
                                                           value="<?php echo htmlspecialchars($vehiculo['placa']); ?>" 
                                                           required maxlength="20">
                                                    <small class="text-muted">Ejemplo: ABC-123</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                                        <option value="">Seleccionar categoría...</option>
                                                        <?php foreach ($categorias as $categoria): ?>
                                                            <option value="<?php echo $categoria['id']; ?>"
                                                                    <?php echo $categoria['id'] == $vehiculo['id_categoria'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="color" class="form-control form-control-color" 
                                                               id="color" name="color" 
                                                               value="<?php echo htmlspecialchars($vehiculo['color']); ?>" 
                                                               required>
                                                        <input type="text" class="form-control" id="color_hex" 
                                                               value="<?php echo htmlspecialchars($vehiculo['color']); ?>" 
                                                               readonly>
                                                    </div>
                                                    <small class="text-muted">Selecciona el color del vehículo</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_conductor" class="form-label">Conductor Asignado</label>
                                                    <select class="form-select" id="id_conductor" name="id_conductor">
                                                        <option value="">Sin conductor asignado</option>
                                                        <?php foreach ($conductores as $conductor): ?>
                                                            <option value="<?php echo $conductor['id']; ?>"
                                                                    <?php echo $conductor['id'] == $vehiculo['id_conductor'] ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($conductor['first_name'] . ' ' . $conductor['last_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <small class="text-muted">Conductor actual: 
                                                        <?php 
                                                        if ($vehiculo['conductor_nombre']) {
                                                            echo htmlspecialchars($vehiculo['conductor_nombre'] . ' ' . $vehiculo['conductor_apellido']);
                                                        } else {
                                                            echo 'Sin asignar';
                                                        }
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <!-- Columna derecha -->
                                            <div class="col-lg-6">
                                                <h4 class="header-title mb-3">Detalles Adicionales</h4>

                                                <div class="mb-3">
                                                    <label for="descripcion" class="form-label">Descripción</label>
                                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="5" 
                                                              placeholder="Información adicional del vehículo (marca, modelo, año, etc.)"><?php echo htmlspecialchars($vehiculo['descripcion'] ?? ''); ?></textarea>
                                                    <small class="text-muted">Información adicional que desees agregar</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="estado" name="estado" required>
                                                        <option value="1" <?php echo $vehiculo['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                                        <option value="0" <?php echo $vehiculo['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                                                    </select>
                                                    <small class="text-muted">Estado actual: 
                                                        <span class="badge <?php echo $vehiculo['estado'] == 1 ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo $vehiculo['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                                        </span>
                                                    </small>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Imágenes Actuales</label>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        <?php 
                                                        $imagenes = json_decode($vehiculo['imagenes'] ?? '[]', true);
                                                        if ($imagenes && count($imagenes) > 0): 
                                                            foreach ($imagenes as $imagen): 
                                                        ?>
                                                            <img src="<?php echo htmlspecialchars($imagen); ?>" 
                                                                 alt="Imagen del vehículo" 
                                                                 class="img-thumbnail" 
                                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                                        <?php 
                                                            endforeach;
                                                        else: 
                                                        ?>
                                                            <p class="text-muted">No hay imágenes registradas</p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="nuevas_imagenes" class="form-label">Agregar Nuevas Imágenes</label>
                                                    <input type="file" class="form-control" id="nuevas_imagenes" 
                                                           name="nuevas_imagenes[]" multiple accept="image/*">
                                                    <small class="text-muted">Puedes seleccionar múltiples imágenes. Se agregarán a las existentes</small>
                                                    
                                                    <!-- Contenedor de previsualización -->
                                                    <div id="preview-container" class="mt-3"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <!-- Información de registro -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <strong>Registrado el:</strong> 
                                                    <?php echo date('d/m/Y H:i:s', strtotime($vehiculo['created_at'])); ?>
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <strong>Última actualización:</strong> 
                                                    <?php echo date('d/m/Y H:i:s', strtotime($vehiculo['updated_at'])); ?>
                                                </small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    <a href="vehiculo-detalle.php?id=<?php echo $id_vehiculo; ?>" class="btn btn-secondary">
                                                        <i class="mdi mdi-arrow-left me-1"></i> Cancelar
                                                    </a>
                                                    <button type="submit" name="submit" class="btn btn-primary">
                                                        <i class="mdi mdi-content-save me-1"></i> Guardar Cambios
                                                    </button>
                                                </div>
                                            </div>
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
        // Sincronizar el selector de color con el input de texto
        document.getElementById('color').addEventListener('input', function() {
            document.getElementById('color_hex').value = this.value;
        });

        // Validación de placa en tiempo real
        document.getElementById('placa').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Previsualización de nuevas imágenes
        document.getElementById('nuevas_imagenes').addEventListener('change', function(e) {
            const previewContainer = document.getElementById('preview-container');
            previewContainer.innerHTML = ''; // Limpiar previsualizaciones anteriores
            
            const files = e.target.files;
            
            if (files.length > 0) {
                const row = document.createElement('div');
                row.className = 'row g-2';
                
                Array.from(files).forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(event) {
                            const col = document.createElement('div');
                            col.className = 'col-md-4 col-sm-6';
                            
                            const imgWrapper = document.createElement('div');
                            imgWrapper.className = 'position-relative';
                            imgWrapper.style.height = '150px';
                            imgWrapper.style.overflow = 'hidden';
                            imgWrapper.style.borderRadius = '8px';
                            imgWrapper.style.border = '2px solid #dee2e6';
                            
                            const img = document.createElement('img');
                            img.src = event.target.result;
                            img.className = 'img-fluid';
                            img.style.width = '100%';
                            img.style.height = '100%';
                            img.style.objectFit = 'cover';
                            
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-success position-absolute top-0 end-0 m-2';
                            badge.textContent = `Nueva ${index + 1}`;
                            
                            imgWrapper.appendChild(img);
                            imgWrapper.appendChild(badge);
                            col.appendChild(imgWrapper);
                            row.appendChild(col);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
                
                previewContainer.appendChild(row);
            }
        });
    </script>
</body>
</html>
