<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-vehiculos.php';
require_once 'includes/Class-mantenimientos.php';

// Verificar si el usuario está logueado
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

$Vehiculo_class = new Vehiculo();
$Mantenimiento_class = new Mantenimiento();
$Usuario_instancia = new Usuario();

// Obtener vehículos y técnicos
$vehiculos = $Vehiculo_class->obtenerVehiculos($_SESSION['user_empresa_id'] ?? null);
$estados = $Mantenimiento_class->obtenerEstadosMantenimiento();
$tecnicos = $Usuario_instancia->obtenerUsuarios(); // Todos los usuarios que puedan hacer mantenimiento

// Si se pasa un id_vehiculo por GET, preseleccionarlo
$id_vehiculo_preseleccionado = $_GET['id_vehiculo'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Registrar Mantenimiento | Sysmaint</title>
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
                                <h4 class="page-title">Registrar Mantenimiento</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Información del Mantenimiento</h4>
                                    
                                    <form method="POST" action="action/add-mantenimientos.php" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="id_vehiculo" class="form-label">Vehículo <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_vehiculo" name="id_vehiculo" required>
                                                        <option value="">Seleccionar vehículo</option>
                                                        <?php foreach ($vehiculos as $vehiculo): ?>
                                                            <option value="<?php echo $vehiculo['id']; ?>"
                                                                <?php echo ($id_vehiculo_preseleccionado == $vehiculo['id']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($vehiculo['placa'] . ' - ' . $vehiculo['categoria_nombre']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_usuario_mantenimiento" class="form-label">Técnico Responsable <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_usuario_mantenimiento" name="id_usuario_mantenimiento" required>
                                                        <option value="">Seleccionar técnico</option>
                                                        <?php foreach ($tecnicos as $tecnico): ?>
                                                            <option value="<?php echo $tecnico['id']; ?>"
                                                                <?php echo ($tecnico['id'] == $_SESSION['user_id']) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($tecnico['first_name'] . ' ' . $tecnico['last_name'] . ' - ' . $tecnico['rol']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="fecha_mantenimiento" class="form-label">Fecha de Mantenimiento <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="fecha_mantenimiento" 
                                                           name="fecha_mantenimiento" value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="proximo_mantenimiento" class="form-label">Próximo Mantenimiento</label>
                                                    <input type="date" class="form-control" id="proximo_mantenimiento" 
                                                           name="proximo_mantenimiento">
                                                    <small class="text-muted">Fecha estimada del próximo mantenimiento</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_estado_mantenimiento" class="form-label">Estado <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_estado_mantenimiento" name="id_estado_mantenimiento" required>
                                                        <?php foreach ($estados as $estado): ?>
                                                            <option value="<?php echo $estado['id']; ?>"
                                                                <?php echo ($estado['nombre'] == 'Completado') ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($estado['nombre']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="costo_mano_obra" class="form-label">Costo de Mano de Obra</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="costo_mano_obra" 
                                                               name="costo_mano_obra" step="0.01" min="0" value="0.00">
                                                    </div>
                                                    <small class="text-muted">Costo del servicio técnico/profesional</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="costo_materiales" class="form-label">Costo de Materiales</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="costo_materiales" 
                                                               name="costo_materiales" step="0.01" min="0" value="0.00" readonly>
                                                    </div>
                                                    <small class="text-muted">Se calculará automáticamente al agregar materiales</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="costo_total" class="form-label">Costo Total</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-success text-white">$</span>
                                                        <input type="number" class="form-control fw-bold" id="costo_total" 
                                                               name="costo_total" step="0.01" min="0" value="0.00" readonly>
                                                    </div>
                                                    <small class="text-muted">Mano de obra + Materiales</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="observaciones" class="form-label">Observaciones</label>
                                                    <textarea class="form-control" id="observaciones" name="observaciones" 
                                                              rows="4" placeholder="Descripción del trabajo realizado, repuestos utilizados, problemas encontrados, etc."></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="imagenes" class="form-label">Imágenes del Mantenimiento</label>
                                                    <input type="file" class="form-control" id="imagenes" 
                                                           name="imagenes[]" multiple accept="image/*">
                                                    <small class="text-muted">Fotos del trabajo realizado (opcional)</small>
                                                </div>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <h5 class="mb-3">Materiales Utilizados (Opcional)</h5>
                                        <div id="materiales-container">
                                            <div class="row material-row mb-2">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="materiales[0][nombre]" 
                                                           placeholder="Nombre del material">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control" name="materiales[0][cantidad]" 
                                                           placeholder="Cantidad" step="0.01" min="0">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" class="form-control" name="materiales[0][unidad]" 
                                                           placeholder="Unidad" value="unidad">
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" name="materiales[0][costo_unitario]" 
                                                               placeholder="Costo unitario" step="0.01" min="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerMaterial(this)" disabled>
                                                        <i class="mdi mdi-delete"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-success btn-sm mb-3" onclick="agregarMaterial()">
                                            <i class="mdi mdi-plus"></i> Agregar Material
                                        </button>

                                        <div class="row mt-4">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary" name="submit">
                                                    <i class="mdi mdi-content-save me-1"></i> Guardar Mantenimiento
                                                </button>
                                                <a href="mantenimientos.php" class="btn btn-secondary">
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
        let materialCount = 1;

        function agregarMaterial() {
            const container = document.getElementById('materiales-container');
            const newRow = document.createElement('div');
            newRow.className = 'row material-row mb-2';
            newRow.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control" name="materiales[${materialCount}][nombre]" 
                           placeholder="Nombre del material">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control material-cantidad" name="materiales[${materialCount}][cantidad]" 
                           placeholder="Cantidad" step="0.01" min="0" oninput="calcularCostos()">
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control" name="materiales[${materialCount}][unidad]" 
                           placeholder="Unidad" value="unidad">
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control material-costo" name="materiales[${materialCount}][costo_unitario]" 
                               placeholder="Costo unitario" step="0.01" min="0" oninput="calcularCostos()">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerMaterial(this)">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            materialCount++;
        }

        function removerMaterial(button) {
            button.closest('.material-row').remove();
            calcularCostos();
        }

        // Calcular costos automáticamente
        function calcularCostos() {
            let totalMateriales = 0;
            
            // Sumar todos los materiales
            document.querySelectorAll('.material-row').forEach(row => {
                const cantidad = parseFloat(row.querySelector('.material-cantidad')?.value || 0);
                const costoUnitario = parseFloat(row.querySelector('.material-costo')?.value || 0);
                totalMateriales += cantidad * costoUnitario;
            });
            
            // Actualizar campo de materiales
            document.getElementById('costo_materiales').value = totalMateriales.toFixed(2);
            
            // Calcular total (mano de obra + materiales)
            const manoObra = parseFloat(document.getElementById('costo_mano_obra').value || 0);
            const total = manoObra + totalMateriales;
            document.getElementById('costo_total').value = total.toFixed(2);
        }

        // Evento para recalcular cuando cambie la mano de obra
        document.getElementById('costo_mano_obra').addEventListener('input', calcularCostos);

        // Auto-calcular fecha de próximo mantenimiento (3 meses después)
        document.getElementById('fecha_mantenimiento').addEventListener('change', function() {
            const fechaActual = new Date(this.value);
            fechaActual.setMonth(fechaActual.getMonth() + 3);
            document.getElementById('proximo_mantenimiento').value = fechaActual.toISOString().split('T')[0];
        });

        // Actualizar los inputs existentes para que también calculen
        document.querySelectorAll('.material-cantidad, .material-costo').forEach(input => {
            input.addEventListener('input', calcularCostos);
        });
    </script>
</body>
</html>
