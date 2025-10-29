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

// Obtener datos del mantenimiento
$mantenimiento = $Mantenimiento_class->obtenerMantenimientoPorId($id_mantenimiento);

if (!$mantenimiento) {
    $_SESSION['error'] = 'Mantenimiento no encontrado.';
    header('Location: mantenimientos.php');
    exit;
}

// Obtener materiales del mantenimiento
$materiales = $Mantenimiento_class->obtenerMaterialesPorMantenimiento($id_mantenimiento);

// Calcular totales
$total_materiales = 0;
foreach ($materiales as $material) {
    $total_materiales += $material['costo_total'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Detalle de Mantenimiento | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <style>
        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            .print-button {
                display: none !important;
            }
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
                        <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
                            <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show no-print" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Título -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right no-print">
                                    <a href="mantenimientos.php" class="btn btn-secondary btn-sm me-2">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                                    </a>
                                    
                                    <!-- Dropdown de cambio rápido de estado -->
                                    <div class="btn-group me-2">
                                        <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-swap-horizontal me-1"></i> Cambiar Estado
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php 
                                            $estados = $Mantenimiento_class->obtenerEstadosMantenimiento();
                                            foreach ($estados as $estado): 
                                            ?>
                                                <li>
                                                    <a class="dropdown-item" href="action/cambiar-estado-mantenimiento.php?id=<?php echo $mantenimiento['id']; ?>&estado=<?php echo $estado['id']; ?>&origen=detalle"
                                                       onclick="return confirm('¿Cambiar estado a <?php echo htmlspecialchars($estado['nombre']); ?>?');">
                                                        <span class="badge" style="background-color: <?php echo $estado['color_hex']; ?>;">
                                                            <?php echo htmlspecialchars($estado['nombre']); ?>
                                                        </span>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    
                                    <a href="update-mantenimientos.php?id=<?php echo $mantenimiento['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="mdi mdi-pencil me-1"></i> Editar
                                    </a>
                                </div>
                                <h4 class="page-title">Detalle de Mantenimiento #<?php echo $mantenimiento['id']; ?></h4>
                            </div>
                        </div>
                    </div>

                    <!-- Información principal del mantenimiento -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Columna izquierda: Información del vehículo -->
                                        <div class="col-lg-6">
                                            <h4 class="header-title mb-3">
                                                <i class="ri-car-line text-primary"></i> Información del Vehículo
                                            </h4>

                                            <div class="mb-3">
                                                <label class="fw-bold d-block">Placa:</label>
                                                <h5 class="text-primary">
                                                    <?php echo htmlspecialchars($mantenimiento['vehiculo_placa']); ?>
                                                </h5>
                                            </div>

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold">Categoría:</label>
                                                        <p><?php echo htmlspecialchars($mantenimiento['vehiculo_categoria']); ?></p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold">Color:</label>
                                                        <p>
                                                            <span class="badge" style="background-color: <?php echo htmlspecialchars($mantenimiento['vehiculo_color']); ?>;">
                                                                <?php echo htmlspecialchars($mantenimiento['vehiculo_color']); ?>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <a href="vehiculo-detalle.php?id=<?php echo $mantenimiento['id_vehiculo']; ?>" 
                                                   class="btn btn-sm btn-outline-primary no-print">
                                                    <i class="mdi mdi-car-info"></i> Ver historial del vehículo
                                                </a>
                                            </div>

                                            <hr>

                                            <h5 class="mb-3">
                                                <i class="ri-user-settings-line text-info"></i> Técnico Responsable
                                            </h5>

                                            <div class="mb-3">
                                                <label class="fw-bold">Nombre:</label>
                                                <p>
                                                    <?php echo htmlspecialchars($mantenimiento['tecnico_nombre'] . ' ' . $mantenimiento['tecnico_apellido']); ?>
                                                </p>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-bold">Email:</label>
                                                <p><?php echo htmlspecialchars($mantenimiento['tecnico_email']); ?></p>
                                            </div>
                                        </div>

                                        <!-- Columna derecha: Detalles del mantenimiento -->
                                        <div class="col-lg-6">
                                            <h4 class="header-title mb-3">
                                                <i class="ri-tools-line text-success"></i> Detalles del Mantenimiento
                                            </h4>

                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold">Fecha de Mantenimiento:</label>
                                                        <p>
                                                            <i class="ri-calendar-line"></i>
                                                            <?php echo date('d/m/Y', strtotime($mantenimiento['fecha_mantenimiento'])); ?>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold">Próximo Mantenimiento:</label>
                                                        <p>
                                                            <?php if ($mantenimiento['proximo_mantenimiento']): ?>
                                                                <i class="ri-calendar-event-line"></i>
                                                                <?php echo date('d/m/Y', strtotime($mantenimiento['proximo_mantenimiento'])); ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">No programado</span>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-bold">Estado:</label>
                                                <p>
                                                    <span class="badge" style="background-color: <?php echo $mantenimiento['estado_color']; ?>; font-size: 16px;">
                                                        <?php echo htmlspecialchars($mantenimiento['estado_nombre']); ?>
                                                    </span>
                                                </p>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-bold">Desglose de Costos:</label>
                                                <div class="card border-light">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block">Mano de Obra</small>
                                                                <h5 class="text-info mb-0">
                                                                    $<?php echo number_format($mantenimiento['costo_mano_obra'] ?? 0, 2); ?>
                                                                </h5>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block">Materiales</small>
                                                                <h5 class="text-warning mb-0">
                                                                    $<?php echo number_format($mantenimiento['costo_materiales'] ?? 0, 2); ?>
                                                                </h5>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <small class="text-muted d-block">Total</small>
                                                                <h4 class="text-success mb-0">
                                                                    <i class="ri-money-dollar-circle-line"></i>
                                                                    $<?php echo number_format($mantenimiento['costo_total'] ?? 0, 2); ?>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="fw-bold">Registrado el:</label>
                                                <p>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i:s', strtotime($mantenimiento['created_at'])); ?>
                                                    </small>
                                                </p>
                                            </div>

                                            <?php if ($mantenimiento['updated_at'] != $mantenimiento['created_at']): ?>
                                            <div class="mb-3">
                                                <label class="fw-bold">Última actualización:</label>
                                                <p>
                                                    <small class="text-muted">
                                                        <?php echo date('d/m/Y H:i:s', strtotime($mantenimiento['updated_at'])); ?>
                                                    </small>
                                                </p>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <?php if ($mantenimiento['observaciones']): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="ri-file-text-line text-warning"></i> Observaciones
                                    </h4>
                                    <div class="bg-light p-3 rounded">
                                        <?php echo nl2br(htmlspecialchars($mantenimiento['observaciones'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Materiales utilizados -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="header-title mb-0">
                                            <i class="ri-settings-3-line text-info"></i> Materiales Utilizados
                                        </h4>
                                        <span class="badge bg-primary">
                                            <?php echo count($materiales); ?> items
                                        </span>
                                    </div>

                                    <?php if (count($materiales) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 5%;">#</th>
                                                        <th style="width: 35%;">Material</th>
                                                        <th style="width: 15%;" class="text-center">Cantidad</th>
                                                        <th style="width: 15%;" class="text-end">Costo Unitario</th>
                                                        <th style="width: 15%;" class="text-end">Costo Total</th>
                                                        <th style="width: 15%;">Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $contador = 1;
                                                    foreach ($materiales as $material): 
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?php echo $contador++; ?></td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($material['nombre_material']); ?></strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo number_format($material['cantidad'], 2); ?>
                                                            <small class="text-muted"><?php echo htmlspecialchars($material['unidad_medida']); ?></small>
                                                        </td>
                                                        <td class="text-end">
                                                            $<?php echo number_format($material['costo_unitario'], 2); ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <strong class="text-success">
                                                                $<?php echo number_format($material['costo_total'], 2); ?>
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                <?php echo $material['observaciones'] ? htmlspecialchars($material['observaciones']) : '-'; ?>
                                                            </small>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="4" class="text-end"><strong>TOTAL:</strong></td>
                                                        <td class="text-end">
                                                            <h5 class="mb-0 text-success">
                                                                $<?php echo number_format($total_materiales, 2); ?>
                                                            </h5>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="ri-tools-line" style="font-size: 48px; opacity: 0.2;"></i>
                                            <p class="text-muted mt-2">No se registraron materiales para este mantenimiento.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Imágenes del mantenimiento -->
                    <?php 
                    $imagenes = json_decode($mantenimiento['imagenes'], true);
                    if ($imagenes && count($imagenes) > 0): 
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="ri-image-line text-danger"></i> Imágenes del Mantenimiento
                                    </h4>
                                    <div class="row">
                                        <?php foreach ($imagenes as $imagen): ?>
                                        <div class="col-md-3 mb-3">
                                            <a href="<?php echo htmlspecialchars($imagen); ?>" target="_blank">
                                                <img src="<?php echo htmlspecialchars($imagen); ?>" 
                                                     class="img-fluid rounded" 
                                                     alt="Imagen del mantenimiento"
                                                     style="cursor: pointer; max-height: 200px; width: 100%; object-fit: cover;">
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Resumen en cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-body text-center">
                                    <i class="ri-file-list-3-line text-primary" style="font-size: 36px;"></i>
                                    <h5 class="mt-2">Materiales</h5>
                                    <h3 class="text-primary"><?php echo count($materiales); ?></h3>
                                    <small class="text-muted">items registrados</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-body text-center">
                                    <i class="ri-money-dollar-circle-line text-success" style="font-size: 36px;"></i>
                                    <h5 class="mt-2">Costo Total</h5>
                                    <h3 class="text-success">$<?php echo number_format($mantenimiento['costo_total'], 2); ?></h3>
                                    <small class="text-muted">en mantenimiento</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="ri-calendar-check-line text-info" style="font-size: 36px;"></i>
                                    <h5 class="mt-2">Próximo</h5>
                                    <h3 class="text-info">
                                        <?php 
                                        if ($mantenimiento['proximo_mantenimiento']) {
                                            $dias = ceil((strtotime($mantenimiento['proximo_mantenimiento']) - time()) / 86400);
                                            echo $dias . ' días';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </h3>
                                    <small class="text-muted">hasta el siguiente</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card" style="border-color: <?php echo $mantenimiento['estado_color']; ?>;">
                                <div class="card-body text-center">
                                    <i class="ri-checkbox-circle-line" style="font-size: 36px; color: <?php echo $mantenimiento['estado_color']; ?>;"></i>
                                    <h5 class="mt-2">Estado</h5>
                                    <h4>
                                        <span class="badge" style="background-color: <?php echo $mantenimiento['estado_color']; ?>;">
                                            <?php echo htmlspecialchars($mantenimiento['estado_nombre']); ?>
                                        </span>
                                    </h4>
                                    <small class="text-muted">del mantenimiento</small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <?php include("includes/footer.php"); ?>
        </div>
    </div>

    <!-- Botón flotante para imprimir -->
    <button onclick="window.print()" class="btn btn-primary btn-lg rounded-circle print-button" title="Imprimir reporte">
        <i class="mdi mdi-printer" style="font-size: 24px;"></i>
    </button>

    <?php include("includes/js.php"); ?>
</body>
</html>
