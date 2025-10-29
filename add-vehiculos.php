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

$Vehiculo_class = new Vehiculo();

// Obtener categorías y conductores
$categorias = $Vehiculo_class->obtenerCategorias();
$conductores = $Vehiculo_class->obtenerConductores($_SESSION['user_empresa_id']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Añadir Vehículo | Sysmaint</title>
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

                    <!-- Título -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <h4 class="page-title">Agregar Vehículo</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Información del Vehículo</h4>
                                    
                                    <form method="POST" action="action/add-vehiculos.php" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="placa" name="placa" 
                                                           placeholder="Ej: ABC-123" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_categoria" name="id_categoria" required>
                                                        <option value="">Seleccionar categoría</option>
                                                        <?php foreach ($categorias as $cat): ?>
                                                            <option value="<?php echo $cat['id']; ?>">
                                                                <?php echo htmlspecialchars($cat['nombre']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="color" class="form-label">Color</label>
                                                    <div class="input-group">
                                                        <input type="color" class="form-control form-control-color" 
                                                               id="color" name="color" value="#6c757d">
                                                        <input type="text" class="form-control" id="color-text" 
                                                               placeholder="Color del vehículo" readonly>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_conductor" class="form-label">Conductor Asignado</label>
                                                    <select class="form-select" id="id_conductor" name="id_conductor">
                                                        <option value="">Sin asignar</option>
                                                        <?php foreach ($conductores as $conductor): ?>
                                                            <option value="<?php echo $conductor['id']; ?>">
                                                                <?php echo htmlspecialchars($conductor['first_name'] . ' ' . $conductor['last_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <small class="text-muted">Opcional: Asigna un conductor al vehículo</small>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="descripcion" class="form-label">Descripción</label>
                                                    <textarea class="form-control" id="descripcion" name="descripcion" 
                                                              rows="5" placeholder="Información adicional del vehículo..."></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="imagenes" class="form-label">Imágenes del Vehículo</label>
                                                    <input type="file" class="form-control" id="imagenes" 
                                                           name="imagenes[]" multiple accept="image/*">
                                                    <small class="text-muted">Puedes seleccionar múltiples imágenes</small>
                                                    
                                                    <!-- Contenedor de previsualización -->
                                                    <div id="preview-container" class="mt-3"></div>
                                                </div>

                                                <div class="mb-3 form-check">
                                                    <input type="checkbox" class="form-check-input" 
                                                           id="estado" name="estado" checked>
                                                    <label class="form-check-label" for="estado">Vehículo Activo</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Campo oculto para la empresa -->
                                        <input type="hidden" name="id_empresa" value="<?php echo $_SESSION['user_empresa_id']; ?>">

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary" name="submit">
                                                    <i class="mdi mdi-content-save me-1"></i> Guardar Vehículo
                                                </button>
                                                <a href="vehiculos.php" class="btn btn-secondary">
                                                    <i class="mdi mdi-close me-1"></i> Cancelar
                                                </a>
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
        // Sincronizar color picker con texto
        document.getElementById('color').addEventListener('input', function() {
            document.getElementById('color-text').value = this.value;
        });

        // Inicializar con el valor por defecto
        document.getElementById('color-text').value = document.getElementById('color').value;

        // Previsualización de imágenes
        document.getElementById('imagenes').addEventListener('change', function(e) {
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
                            badge.className = 'badge bg-primary position-absolute top-0 end-0 m-2';
                            badge.textContent = `Imagen ${index + 1}`;
                            
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
