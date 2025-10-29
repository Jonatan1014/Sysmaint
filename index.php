<?php
// index.php - Dashboard principal
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-vehiculos.php';
require_once 'includes/Class-mantenimientos.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

// Obtener ID de empresa del usuario logueado
$id_empresa = $_SESSION['user_empresa_id'] ?? null;

// Instanciar clases
$Vehiculo_class = new Vehiculo();
$Mantenimiento_class = new Mantenimiento();

// Obtener datos del dashboard
$dashboard = $Mantenimiento_class->obtenerDashboardCompleto($id_empresa);
$estadisticas_vehiculos = $Vehiculo_class->obtenerEstadisticas($id_empresa);
?>

<!DOCTYPE html>
<html lang="es">
<!-- Mirrored from coderthemes.com/hyper/saas/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 17 Oct 2023 20:31:50 GMT -->

<head>
    <meta charset="utf-8" />
    <title>Inicio| Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Daterangepicker css -->
    <link rel="stylesheet" href="assets/vendor/daterangepicker/daterangepicker.css" />

    <!-- Vector Map css -->
    <link rel="stylesheet" href="assets/vendor/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css" />

    <!-- Theme Config Js -->
    <script src="assets/js/hyper-config.js"></script>

    <!-- App css -->
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />

    <!-- Custom Dashboard CSS -->
    <style>
    .widget-rounded-circle .card-body {
        padding: 1.5rem;
    }

    .widget-rounded-circle .avatar-lg {
        height: 4rem;
        width: 4rem;
    }

    .widget-rounded-circle .avatar-title {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card {
        box-shadow: 0 0 35px 0 rgba(154, 161, 171, .15);
        margin-bottom: 24px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(67, 94, 190, 0.05);
    }

    .badge {
        padding: 0.35em 0.65em;
        font-weight: 600;
    }

    @media print {

        .page-title-right,
        .sidebar,
        .topnav,
        .navbar-custom,
        .footer {
            display: none !important;
        }

        .content-page {
            margin-left: 0 !important;
            padding-top: 0 !important;
        }

        .card {
            page-break-inside: avoid;
        }
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    canvas {
        max-height: 400px;
    }
    </style>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">
        <!-- ========== Topbar Start ========== -->
        <?php
        include("includes/header.php");
        ?>
        <!-- ========== Topbar End ========== -->

        <?php
        include("includes/sidebar.php");
        ?>


        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">
                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- Page Title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success btn-sm dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="mdi mdi-download"></i> Exportar Reporte
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="exportar-reporte.php?formato=csv"
                                                    target="_blank">
                                                    <i class="mdi mdi-file-delimited"></i> Exportar como CSV
                                                </a></li>
                                            <li><a class="dropdown-item" href="#" onclick="exportarReporteTXT()">
                                                    <i class="mdi mdi-file-document"></i> Exportar como TXT
                                                </a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li><a class="dropdown-item" href="#" onclick="window.print()">
                                                    <i class="mdi mdi-printer"></i> Imprimir
                                                </a></li>
                                        </ul>
                                    </div>
                                </div>
                                <h4 class="page-title">Dashboard de Mantenimiento</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Principales -->
                    <div class="row">
                        <!-- Total Vehículos -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                                <i class="fe-truck font-22 avatar-title text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    <span
                                                        data-plugin="counterup"><?php echo $estadisticas_vehiculos['total_vehiculos']; ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Vehículos</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Mantenimientos -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                                <i class="fe-tool font-22 avatar-title text-success"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    <span
                                                        data-plugin="counterup"><?php echo $dashboard['estadisticas_generales']['total_mantenimientos']; ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Mantenimientos</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Próximos Mantenimientos -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                                <i class="fe-clock font-22 avatar-title text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    <span
                                                        data-plugin="counterup"><?php echo $dashboard['estadisticas_generales']['proximos_30_dias']; ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Próximos 30 días</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Costo Total -->
                        <div class="col-md-6 col-xl-3">
                            <div class="widget-rounded-circle card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                                                <i class="fe-dollar-sign font-22 avatar-title text-danger"></i>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <h3 class="text-dark mt-1">
                                                    $<span
                                                        data-plugin="counterup"><?php echo number_format($dashboard['estadisticas_generales']['costo_total'], 0); ?></span>
                                                </h3>
                                                <p class="text-muted mb-1 text-truncate">Costo Total</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Gráficas -->
                    <div class="row">
                        <!-- Costos Mensuales -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Costos de Mantenimiento Mensual</h4>
                                    <canvas id="grafica-costos-mensuales" height="100"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Estados de Mantenimiento -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Distribución por Estado</h4>
                                    <canvas id="grafica-estados" height="280"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: Vehículos con más mantenimientos -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Vehículos con Más Mantenimientos</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Placa</th>
                                                    <th>Categoría</th>
                                                    <th>Cantidad</th>
                                                    <th>Costo Total</th>
                                                    <th>Promedio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dashboard['vehiculos_mas_mantenimiento'] as $vehiculo): ?>
                                                <tr>
                                                    <td>
                                                        <a
                                                            href="vehiculo-detalle.php?id=<?php echo $vehiculo['id']; ?>">
                                                            <?php echo htmlspecialchars($vehiculo['placa']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($vehiculo['categoria']); ?></td>
                                                    <td><span
                                                            class="badge bg-primary"><?php echo $vehiculo['total_mantenimientos']; ?></span>
                                                    </td>
                                                    <td>$<?php echo number_format($vehiculo['costo_total_acumulado'], 2); ?>
                                                    </td>
                                                    <td>$<?php echo number_format($vehiculo['costo_promedio'], 2); ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Materiales Más Comprados -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Materiales Más Comprados</h4>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Material</th>
                                                    <th>Cantidad</th>
                                                    <th>Veces Usado</th>
                                                    <th>Costo Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dashboard['materiales_mas_comprados'] as $material): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($material['nombre_material']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo number_format($material['cantidad_total'], 2); ?>
                                                        <?php echo htmlspecialchars($material['unidad_medida']); ?>
                                                    </td>
                                                    <td><span
                                                            class="badge bg-info"><?php echo $material['veces_usado']; ?></span>
                                                    </td>
                                                    <td>$<?php echo number_format($material['costo_total'], 2); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: Próximos Mantenimientos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">
                                        <i class="mdi mdi-calendar-clock text-warning"></i>
                                        Próximos Mantenimientos (30 días)
                                    </h4>
                                    <?php if (count($dashboard['proximos_mantenimientos']) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Vehículo</th>
                                                    <th>Categoría</th>
                                                    <th>Último Mantenimiento</th>
                                                    <th>Próximo Mantenimiento</th>
                                                    <th>Días Restantes</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dashboard['proximos_mantenimientos'] as $proximo): 
                                                    $dias_restantes = ceil((strtotime($proximo['proximo_mantenimiento']) - time()) / 86400);
                                                    $clase_urgencia = $dias_restantes <= 7 ? 'danger' : ($dias_restantes <= 15 ? 'warning' : 'success');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a
                                                            href="vehiculo-detalle.php?id=<?php echo $proximo['id_vehiculo']; ?>">
                                                            <?php echo htmlspecialchars($proximo['vehiculo_placa']); ?>
                                                        </a>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($proximo['vehiculo_categoria']); ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($proximo['fecha_mantenimiento'])); ?>
                                                    </td>
                                                    <td><?php echo date('d/m/Y', strtotime($proximo['proximo_mantenimiento'])); ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $clase_urgencia; ?>">
                                                            <?php echo $dias_restantes; ?> días
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge"
                                                            style="background-color: <?php echo $proximo['estado_color']; ?>">
                                                            <?php echo htmlspecialchars($proximo['estado_nombre']); ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information"></i>
                                        No hay mantenimientos programados para los próximos 30 días.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row 5: Análisis de Costos por Vehículo -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Análisis de Costos por Vehículo</h4>
                                    <canvas id="grafica-costos-vehiculos" height="80"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- container -->
            </div>
            <!-- content -->

            <!-- Footer Start -->
            <?php
            include("includes/footer.php");
            ?>
            <!-- end Footer -->
        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <?php
    include("includes/js.php");
    ?>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Datos para las gráficas
    const datosCostosMensuales = <?php echo json_encode($dashboard['costo_mensual']); ?>;
    const datosEstados = <?php echo json_encode($dashboard['estadisticas_generales']['por_estado']); ?>;
    const datosCostosVehiculos = <?php echo json_encode($dashboard['costos_por_vehiculo']); ?>;

    // Gráfica de Costos Mensuales
    const ctxCostosMensuales = document.getElementById('grafica-costos-mensuales').getContext('2d');
    new Chart(ctxCostosMensuales, {
        type: 'line',
        data: {
            labels: datosCostosMensuales.map(d => d.mes).reverse(),
            datasets: [{
                label: 'Mano de Obra',
                data: datosCostosMensuales.map(d => parseFloat(d.total_mano_obra)).reverse(),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Materiales',
                data: datosCostosMensuales.map(d => parseFloat(d.total_materiales)).reverse(),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Total',
                data: datosCostosMensuales.map(d => parseFloat(d.total_costo)).reverse(),
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Gráfica de Estados (Pie Chart)
    const ctxEstados = document.getElementById('grafica-estados').getContext('2d');
    new Chart(ctxEstados, {
        type: 'doughnut',
        data: {
            labels: datosEstados.map(e => e.nombre),
            datasets: [{
                data: datosEstados.map(e => parseInt(e.cantidad)),
                backgroundColor: datosEstados.map(e => e.color),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Gráfica de Costos por Vehículo (Bar Chart)
    const top10Vehiculos = datosCostosVehiculos.slice(0, 10);
    const ctxCostosVehiculos = document.getElementById('grafica-costos-vehiculos').getContext('2d');
    new Chart(ctxCostosVehiculos, {
        type: 'bar',
        data: {
            labels: top10Vehiculos.map(v => v.placa),
            datasets: [{
                label: 'Mano de Obra',
                data: top10Vehiculos.map(v => parseFloat(v.total_mano_obra || 0)),
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }, {
                label: 'Materiales',
                data: top10Vehiculos.map(v => parseFloat(v.total_materiales || 0)),
                backgroundColor: 'rgba(255, 99, 132, 0.7)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
                        },
                        footer: function(items) {
                            const total = items.reduce((sum, item) => sum + item.parsed.y, 0);
                            return 'Total: $' + total.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                x: {
                    stacked: true
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Función para exportar reporte como TXT
    function exportarReporteTXT() {
        // Crear contenido del reporte
        let contenido = 'REPORTE DE MANTENIMIENTO\n';
        contenido += '='.repeat(80) + '\n\n';
        contenido += 'Fecha de generación: ' + new Date().toLocaleString() + '\n\n';

        // Estadísticas Generales
        contenido += 'ESTADÍSTICAS GENERALES\n';
        contenido += '-'.repeat(80) + '\n';
        contenido += 'Total de Vehículos: <?php echo $estadisticas_vehiculos['total_vehiculos']; ?>\n';
        contenido +=
            'Total de Mantenimientos: <?php echo $dashboard['estadisticas_generales']['total_mantenimientos']; ?>\n';
        contenido +=
            'Próximos Mantenimientos (30 días): <?php echo $dashboard['estadisticas_generales']['proximos_30_dias']; ?>\n';
        contenido +=
            'Costo Total: $<?php echo number_format($dashboard['estadisticas_generales']['costo_total'], 2); ?>\n\n';

        // Vehículos con más mantenimientos
        contenido += 'VEHÍCULOS CON MÁS MANTENIMIENTOS\n';
        contenido += '-'.repeat(80) + '\n';
        <?php foreach ($dashboard['vehiculos_mas_mantenimiento'] as $v): ?>
        contenido +=
            '<?php echo str_pad($v['placa'], 15); ?> - <?php echo str_pad($v['categoria'], 20); ?> - <?php echo str_pad($v['total_mantenimientos'] . ' mantenimientos', 20); ?> - $<?php echo number_format($v['costo_total_acumulado'], 2); ?>\n';
        <?php endforeach; ?>
        contenido += '\n';

        // Materiales más comprados
        contenido += 'MATERIALES MÁS COMPRADOS\n';
        contenido += '-'.repeat(80) + '\n';
        <?php foreach ($dashboard['materiales_mas_comprados'] as $m): ?>
        contenido +=
            '<?php echo str_pad($m['nombre_material'], 30); ?> - <?php echo str_pad(number_format($m['cantidad_total'], 2) . ' ' . $m['unidad_medida'], 20); ?> - $<?php echo number_format($m['costo_total'], 2); ?>\n';
        <?php endforeach; ?>

        // Descargar como archivo de texto
        const blob = new Blob([contenido], {
            type: 'text/plain'
        });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'reporte_mantenimiento_' + new Date().toISOString().split('T')[0] + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    // Animación de contadores
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('[data-plugin="counterup"]');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.ceil(current);
                }
            }, 20);
        });
    });
    </script>

</body>

</html>