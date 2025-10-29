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

// Obtener ID de empresa del usuario logueado
$id_empresa = $_SESSION['user_empresa_id'] ?? null;

// Instanciar clase
$Vehiculo_class = new Vehiculo();

// Obtener datos mediante métodos de la clase
$vehiculos = $Vehiculo_class->obtenerVehiculos($id_empresa);
$estadisticas = $Vehiculo_class->obtenerEstadisticas($id_empresa);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Vehículos | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta content="Sistema de gestión de vehículos" name="description" />
    <meta content="Coderthemes" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico" />

    <!-- Datatables css -->
    <link href="assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />

    <!-- Theme Config Js -->
    <script src="assets/js/hyper-config.js"></script>

    <!-- App css -->
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
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
                                <h4 class="page-title">Gestión de Vehículos</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas rápidas -->
                    <div class="row mb-3">
                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Total Vehículos</h5>
                                            <h3 class="my-2 py-1"><?php echo $estadisticas['total_vehiculos']; ?></h3>
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

                        <div class="col-md-6 col-xl-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Categorías</h5>
                                            <h3 class="my-2 py-1"><?php echo $estadisticas['total_categorias']; ?></h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-stack-line" style="font-size: 48px; opacity: 0.3;"></i>
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
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Con Conductor</h5>
                                            <h3 class="my-2 py-1"><?php echo $estadisticas['con_conductor']; ?></h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-user-line" style="font-size: 48px; opacity: 0.3;"></i>
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
                                            <h5 class="text-muted fw-normal mt-0 text-truncate">Sin Conductor</h5>
                                            <h3 class="my-2 py-1"><?php echo $estadisticas['sin_conductor']; ?></h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-user-unfollow-line" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de vehículos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="header-title">Listado de Vehículos</h4>
                                        <a href="add-vehiculos.php" class="btn btn-primary">
                                            <i class="mdi mdi-plus-circle me-1"></i> Agregar Vehículo
                                        </a>
                                    </div>

                                    <div class="table-responsive">
                                        <table id="vehiculos-table" class="table table-striped dt-responsive nowrap w-100">
                                            <thead>
                                                <tr>
                                                    <th>Placa</th>
                                                    <th>Categoría</th>
                                                    <th>Color</th>
                                                    <th>Conductor</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($vehiculos as $vehiculo): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($vehiculo['placa']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($vehiculo['categoria_nombre'] ?? 'Sin categoría'); ?></td>
                                                    <td>
                                                        <span class="badge" style="background-color: <?php echo htmlspecialchars($vehiculo['color'] ?? '#6c757d'); ?>;">
                                                            <?php echo htmlspecialchars($vehiculo['color'] ?? 'N/A'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if ($vehiculo['conductor_nombre']) {
                                                            echo htmlspecialchars($vehiculo['conductor_nombre'] . ' ' . $vehiculo['conductor_apellido']);
                                                        } else {
                                                            echo '<span class="text-muted">Sin asignar</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($vehiculo['estado'] == 1): ?>
                                                            <span class="badge bg-success">Activo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="vehiculo-detalle.php?id=<?php echo $vehiculo['id']; ?>" 
                                                           class="btn btn-sm btn-info" title="Ver detalles">
                                                            <i class="mdi mdi-eye"></i>
                                                        </a>
                                                        <a href="update-vehiculos.php?id=<?php echo $vehiculo['id']; ?>" 
                                                           class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="mdi mdi-pencil"></i>
                                                        </a>
                                                        <button onclick="eliminarVehiculo(<?php echo $vehiculo['id']; ?>)" 
                                                                class="btn btn-sm btn-danger" title="Eliminar">
                                                            <i class="mdi mdi-delete"></i>
                                                        </button>
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

    <!-- Modal de confirmación -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este vehículo? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
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
            $('#vehiculos-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                responsive: true,
                order: [[0, 'asc']]
            });
        });

        let deleteId = null;

        function eliminarVehiculo(id) {
            deleteId = id;
            var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'action/delete-vehiculos.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id';
            input.value = deleteId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        });
    </script>
</body>
</html>
