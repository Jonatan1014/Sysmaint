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

// Obtener ID de empresa del usuario logueado
$id_empresa = $_SESSION['user_empresa_id'] ?? null;

// Instanciar clases
$Mantenimiento_class = new Mantenimiento();
$Vehiculo_class = new Vehiculo();

// Obtener datos mediante métodos de las clases
$mantenimientos = $Mantenimiento_class->obtenerMantenimientos($id_empresa);
$estados = $Mantenimiento_class->obtenerEstadosMantenimiento();
$proximosMantenimientos = $Mantenimiento_class->obtenerProximosMantenimientos($id_empresa, 30);
$estadisticas = $Mantenimiento_class->obtenerEstadisticas($id_empresa);
$totalVehiculos = $Vehiculo_class->contarVehiculos($id_empresa);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Mantenimientos | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
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
                                <h4 class="page-title">Gestión de Mantenimientos</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas -->
                    <div class="row mb-3">
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Total Mantenimientos
                                            </h5>
                                            <h3 class="my-2 py-1"><?php echo $estadisticas['total_mantenimientos']; ?>
                                            </h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-tools-line" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Próximos (30 días)</h5>
                                            <h3 class="my-2 py-1"><?php echo $estadisticas['proximos_30_dias']; ?></h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-calendar-check-line"
                                                    style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Costo Total</h5>
                                            <h3 class="my-2 py-1">
                                                $<?php echo number_format($estadisticas['costo_total'], 2); ?>
                                            </h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-money-dollar-circle-line"
                                                    style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Vehículos</h5>
                                            <h3 class="my-2 py-1"><?php echo $totalVehiculos; ?></h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-car-line" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Próximos mantenimientos -->
                    <?php if (count($proximosMantenimientos) > 0): ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="ri-alarm-warning-line text-warning"></i>
                                        Próximos Mantenimientos Programados
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Vehículo</th>
                                                    <th>Fecha Programada</th>
                                                    <th>Días Restantes</th>
                                                    <th>Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($proximosMantenimientos as $proximo): 
                                                    $dias = ceil((strtotime($proximo['proximo_mantenimiento']) - time()) / 86400);
                                                    $badgeClass = $dias <= 7 ? 'danger' : ($dias <= 15 ? 'warning' : 'info');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($proximo['vehiculo_placa']); ?></strong>
                                                        <br>
                                                        <small
                                                            class="text-muted"><?php echo htmlspecialchars($proximo['vehiculo_categoria']); ?></small>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($proximo['proximo_mantenimiento'])); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $badgeClass; ?>">
                                                            <?php echo $dias; ?> día<?php echo $dias != 1 ? 's' : ''; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="add-mantenimientos.php?id_vehiculo=<?php echo $proximo['id_vehiculo']; ?>"
                                                            class="btn btn-sm btn-success">
                                                            <i class="mdi mdi-plus"></i> Registrar
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tabla de mantenimientos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="header-title">Historial de Mantenimientos</h4>
                                        <a href="add-mantenimientos.php" class="btn btn-primary">
                                            <i class="mdi mdi-plus-circle me-1"></i> Registrar Mantenimiento
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="mantenimientos-table"
                                            class="table table-striped dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Vehículo</th>
                                                    <th>Categoría</th>
                                                    <th>Técnico</th>
                                                    <th>Estado</th>
                                                    <th>Mano de Obra</th>
                                                    <th>Materiales</th>
                                                    <th>Total</th>
                                                    <th>Próximo</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($mantenimientos as $mant): ?>
                                                <tr>
                                                    <td><?php echo date('d/m/Y', strtotime($mant['fecha_mantenimiento'])); ?>
                                                    </td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($mant['vehiculo_placa']); ?></strong>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($mant['vehiculo_categoria']); ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($mant['tecnico_nombre'] . ' ' . $mant['tecnico_apellido']); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge"
                                                            style="background-color: <?php echo $mant['estado_color']; ?>;">
                                                            <?php echo htmlspecialchars($mant['estado_nombre']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        $<?php echo number_format($mant['costo_mano_obra'] ?? 0, 2); ?>
                                                    </td>
                                                    <td>
                                                        $<?php echo number_format($mant['costo_materiales'] ?? 0, 2); ?>
                                                    </td>
                                                    <td>
                                                        <strong>$<?php echo number_format($mant['costo_total'] ?? 0, 2); ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php if ($mant['proximo_mantenimiento']): ?>
                                                        <?php echo date('d/m/Y', strtotime($mant['proximo_mantenimiento'])); ?>
                                                        <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="mantenimiento-detalle.php?id=<?php echo $mant['id']; ?>"
                                                            class="btn btn-sm btn-info" title="Ver detalles">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="update-mantenimientos.php?id=<?php echo $mant['id']; ?>"
                                                            class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>

                                                        <!-- Dropdown de cambio rápido de estado -->
                                                        <div class="btn-group">
                                                            <button type="button"
                                                                class="btn btn-sm btn-secondary dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                                title="Cambiar estado">
                                                                <i class="mdi mdi-swap-horizontal"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <?php foreach ($estados as $estado): ?>
                                                                <li>
                                                                    <a class="dropdown-item <?php echo $estado['id'] == $mant['id_estado_mantenimiento'] ? 'active' : ''; ?>"
                                                                        href="action/cambiar-estado-mantenimiento.php?id=<?php echo $mant['id']; ?>&estado=<?php echo $estado['id']; ?>&origen=mantenimientos">
                                                                        <span class="badge"
                                                                            style="background-color: <?php echo $estado['color_hex']; ?>;">
                                                                            <?php echo htmlspecialchars($estado['nombre']); ?>
                                                                        </span>
                                                                    </a>
                                                                </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
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

    <!-- Datatables js -->
    <script src="assets/vendor/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#mantenimientos-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
            },
            responsive: true,
            order: [
                [0, 'desc']
            ] // Ordenar por fecha descendente
        });
    });
    </script>
</body>

</html>