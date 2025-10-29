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

// Verificar que se haya pasado un ID
if (!isset($_GET['id'])) {
    $_SESSION['error'] = 'ID de mantenimiento no proporcionado.';
    header('Location: mantenimientos.php');
    exit;
}

$id_mantenimiento = $_GET['id'];

$Mantenimiento_class = new Mantenimiento();
$Vehiculo_class = new Vehiculo();

// Obtener datos del mantenimiento
$mantenimiento = $Mantenimiento_class->obtenerMantenimientoPorId($id_mantenimiento);

if (!$mantenimiento) {
    $_SESSION['error'] = 'Mantenimiento no encontrado.';
    header('Location: mantenimientos.php');
    exit;
}

// Obtener vehículos, técnicos y estados
$vehiculos = $Vehiculo_class->obtenerVehiculos($_SESSION['user_empresa_id'] ?? null);
$tecnicos = $Mantenimiento_class->obtenerTecnicos($_SESSION['user_empresa_id'] ?? null);
$estados = $Mantenimiento_class->obtenerEstadosMantenimiento();

// Obtener materiales del mantenimiento
$materiales = $Mantenimiento_class->obtenerMaterialesPorMantenimiento($id_mantenimiento);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Editar Mantenimiento | Sysmaint</title>
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
                                    <a href="mantenimiento-detalle.php?id=<?php echo $id_mantenimiento; ?>"
                                        class="btn btn-secondary btn-sm">
                                        <i class="mdi mdi-arrow-left me-1"></i> Cancelar
                                    </a>
                                </div>
                                <h4 class="page-title">Editar Mantenimiento #<?php echo $id_mantenimiento; ?></h4>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="action/update-mantenimientos.php" method="POST"
                                        enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $id_mantenimiento; ?>">

                                        <div class="row">
                                            <!-- Columna izquierda -->
                                            <div class="col-lg-6">
                                                <h4 class="header-title mb-3">Información General</h4>

                                                <div class="mb-3">
                                                    <label for="id_vehiculo" class="form-label">Vehículo <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_vehiculo" name="id_vehiculo"
                                                        required>
                                                        <option value="">Seleccionar vehículo...</option>
                                                        <?php foreach ($vehiculos as $vehiculo): ?>
                                                        <option value="<?php echo $vehiculo['id']; ?>"
                                                            <?php echo $vehiculo['id'] == $mantenimiento['id_vehiculo'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($vehiculo['placa'] . ' - ' . $vehiculo['categoria_nombre']); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_usuario_mantenimiento" class="form-label">Técnico
                                                        Responsable <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_usuario_mantenimiento"
                                                        name="id_usuario_mantenimiento" required>
                                                        <option value="">Seleccionar técnico...</option>
                                                        <?php foreach ($tecnicos as $tecnico): ?>
                                                        <option value="<?php echo $tecnico['id']; ?>"
                                                            <?php echo $tecnico['id'] == $mantenimiento['id_usuario_mantenimiento'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($tecnico['nombre_completo']); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="id_estado_mantenimiento" class="form-label">Estado <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="id_estado_mantenimiento"
                                                        name="id_estado_mantenimiento" required>
                                                        <?php foreach ($estados as $estado): ?>
                                                        <option value="<?php echo $estado['id']; ?>"
                                                            data-color="<?php echo $estado['color_hex']; ?>"
                                                            <?php echo $estado['id'] == $mantenimiento['id_estado_mantenimiento'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($estado['nombre']); ?>
                                                        </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <small class="text-muted">Estado actual:
                                                        <span class="badge"
                                                            style="background-color: <?php echo $mantenimiento['estado_color']; ?>;">
                                                            <?php echo htmlspecialchars($mantenimiento['estado_nombre']); ?>
                                                        </span>
                                                    </small>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="fecha_mantenimiento" class="form-label">Fecha de
                                                                Mantenimiento <span class="text-danger">*</span></label>
                                                            <input type="date" class="form-control"
                                                                id="fecha_mantenimiento" name="fecha_mantenimiento"
                                                                value="<?php echo date('Y-m-d', strtotime($mantenimiento['fecha_mantenimiento'])); ?>"
                                                                required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="proximo_mantenimiento"
                                                                class="form-label">Próximo Mantenimiento</label>
                                                            <input type="date" class="form-control"
                                                                id="proximo_mantenimiento" name="proximo_mantenimiento"
                                                                value="<?php echo $mantenimiento['proximo_mantenimiento'] ? date('Y-m-d', strtotime($mantenimiento['proximo_mantenimiento'])) : ''; ?>">
                                                            <small class="text-muted">Opcional</small>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="observaciones" class="form-label">Observaciones</label>
                                                    <textarea class="form-control" id="observaciones"
                                                        name="observaciones" rows="4"
                                                        placeholder="Descripción del trabajo realizado, problemas encontrados, recomendaciones..."><?php echo htmlspecialchars($mantenimiento['observaciones'] ?? ''); ?></textarea>
                                                </div>
                                            </div>

                                            <!-- Columna derecha -->
                                            <div class="col-lg-6">
                                                <h4 class="header-title mb-3">Costos del Mantenimiento</h4>

                                                <div class="mb-3">
                                                    <label for="costo_mano_obra" class="form-label">Costo de Mano de
                                                        Obra</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="costo_mano_obra"
                                                            name="costo_mano_obra" step="0.01" min="0"
                                                            value="<?php echo number_format($mantenimiento['costo_mano_obra'] ?? 0, 2, '.', ''); ?>"
                                                            placeholder="0.00">
                                                    </div>
                                                    <small class="text-muted">Pago al técnico por el servicio</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="costo_materiales" class="form-label">Costo de
                                                        Materiales</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control bg-light"
                                                            id="costo_materiales" name="costo_materiales" step="0.01"
                                                            value="<?php echo number_format($mantenimiento['costo_materiales'] ?? 0, 2, '.', ''); ?>"
                                                            readonly>
                                                    </div>
                                                    <small class="text-muted">Se calcula automáticamente según los
                                                        materiales</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="costo_total" class="form-label">Costo Total</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control bg-light fw-bold"
                                                            id="costo_total" step="0.01"
                                                            value="<?php echo number_format($mantenimiento['costo_total'] ?? 0, 2, '.', ''); ?>"
                                                            readonly>
                                                    </div>
                                                    <small class="text-muted">Mano de obra + Materiales</small>
                                                </div>

                                                <hr class="my-4">

                                                <h5 class="mb-3">
                                                    <i class="ri-settings-3-line"></i> Materiales Utilizados
                                                    <button type="button" class="btn btn-sm btn-success float-end"
                                                        onclick="agregarMaterial()">
                                                        <i class="mdi mdi-plus"></i> Agregar Material
                                                    </button>
                                                </h5>

                                                <div id="materiales-container">
                                                    <?php if (count($materiales) > 0): ?>
                                                    <?php foreach ($materiales as $index => $material): ?>
                                                    <div class="row material-row mb-2">
                                                        <input type="hidden"
                                                            name="materiales[<?php echo $index; ?>][id]"
                                                            value="<?php echo $material['id']; ?>">
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control"
                                                                name="materiales[<?php echo $index; ?>][nombre]"
                                                                placeholder="Nombre del material"
                                                                value="<?php echo htmlspecialchars($material['nombre_material']); ?>">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" class="form-control material-cantidad"
                                                                name="materiales[<?php echo $index; ?>][cantidad]"
                                                                placeholder="Cantidad" step="0.01" min="0"
                                                                value="<?php echo $material['cantidad']; ?>"
                                                                oninput="calcularCostos()">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="form-control"
                                                                name="materiales[<?php echo $index; ?>][unidad]"
                                                                placeholder="Unidad"
                                                                value="<?php echo htmlspecialchars($material['unidad_medida']); ?>">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" class="form-control material-costo"
                                                                    name="materiales[<?php echo $index; ?>][costo_unitario]"
                                                                    placeholder="Costo" step="0.01" min="0"
                                                                    value="<?php echo $material['costo_unitario']; ?>"
                                                                    oninput="calcularCostos()">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-danger btn-sm w-100"
                                                                onclick="removerMaterial(this)">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                    <?php else: ?>
                                                    <div class="row material-row mb-2">
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control"
                                                                name="materiales[0][nombre]"
                                                                placeholder="Nombre del material">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="number" class="form-control material-cantidad"
                                                                name="materiales[0][cantidad]" placeholder="Cantidad"
                                                                step="0.01" min="0" oninput="calcularCostos()">
                                                        </div>
                                                        <div class="col-md-2">
                                                            <input type="text" class="form-control"
                                                                name="materiales[0][unidad]" placeholder="Unidad"
                                                                value="unidad">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="input-group">
                                                                <span class="input-group-text">$</span>
                                                                <input type="number" class="form-control material-costo"
                                                                    name="materiales[0][costo_unitario]"
                                                                    placeholder="Costo unitario" step="0.01" min="0"
                                                                    oninput="calcularCostos()">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <button type="button" class="btn btn-danger btn-sm w-100"
                                                                onclick="removerMaterial(this)">
                                                                <i class="mdi mdi-delete"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                </div>

                                                <small class="text-muted">
                                                    <i class="mdi mdi-information"></i> Los materiales se pueden
                                                    agregar, modificar o eliminar
                                                </small>
                                            </div>
                                        </div>

                                        <hr class="my-4">

                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between">
                                                    <a href="mantenimiento-detalle.php?id=<?php echo $id_mantenimiento; ?>"
                                                        class="btn btn-secondary">
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
    let materialCount = <?php echo count($materiales); ?>;

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

    // Calcular costos al cargar la página
    document.addEventListener('DOMContentLoaded', calcularCostos);
    </script>
</body>

</html>