<?php
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-vehiculos.php';

// Verificar si el usuario está logueado
if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');      
    exit;
}

// Obtener datos de usuarios y vehículos
$Usuario_class = new Usuario();
$Vehiculo_class = new Vehiculo();

// Obtener usuarios (filtrar por empresa del usuario logueado si es necesario)
$usuarios = $Usuario_class->obtenerUsuarios();

// Obtener vehículos (filtrar por empresa del usuario logueado si es necesario)
$vehiculos = $Vehiculo_class->obtenerVehiculos($_SESSION['user_empresa_id'] ?? null);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Admin | Sysmaint</title>
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
                    
                    <!-- Mensajes de éxito/error -->
                    <?php if (isset($_SESSION['exito'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['exito']; unset($_SESSION['exito']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box">
                                <div class="page-title-right">
                                    <form class="d-flex">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-light"
                                                id="dash-daterange" />
                                            <span class="input-group-text bg-primary border-primary text-white">
                                                <i class="mdi mdi-calendar-range font-13"></i>
                                            </span>
                                        </div>
                                    </form>
                                </div>
                                <h4 class="page-title">Administración</h4>
                            </div>
                        </div>
                    </div>

                    <!--tabla de vehículos (antes equipos)-->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="header-title">Administrar vehículos</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-5">
                                            <a href="add-vehiculos.php" class="btn btn-success mb-2"><i
                                                    class="mdi mdi-plus-circle me-2"></i> Agregar vehículo</a>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="text-sm-end">
                                                <button type="button" class="btn btn-light mb-2 me-1">Importar</button>
                                                <button type="button" class="btn btn-light mb-2">Exportar</button>
                                            </div>
                                        </div><!-- end col-->
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-centered w-100 dt-responsive nowrap"
                                            id="products-datatable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="all" style="width: 20px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="customCheck1">
                                                            <label class="form-check-label"
                                                                for="customCheck1">&nbsp;</label>
                                                        </div>
                                                    </th>
                                                    <th class="all">Placa</th>
                                                    <th>Categoría</th>
                                                    <th>Color</th>
                                                    <th>Descripción</th>
                                                    <th>Conductor</th>
                                                    <th>Estado</th>
                                                    <th style="width: 85px;">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($vehiculos as $vehiculo): ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="customCheck2">
                                                            <label class="form-check-label"
                                                                for="customCheck2">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <img src="assets/images/equipos/pc.png" alt="contact-img"
                                                            title="contact-img" class="rounded me-3" height="48" />
                                                        <p class="m-0 d-inline-block align-middle font-16">
                                                            <a href="update-vehiculos.php?id=<?php echo $vehiculo['id']; ?>"
                                                                class="text-body"><?php echo $vehiculo['placa']; ?></a>
                                                        </p>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($vehiculo['categoria_nombre']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($vehiculo['color']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($vehiculo['descripcion']); ?>
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
                                                        <span class="badge <?php echo $vehiculo['estado'] == 1 ? 'bg-success' : 'bg-danger'; ?>">
                                                            <?php echo $vehiculo['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                                        </span>
                                                    </td>

                                                    <td class="table-action">
                                                        <a href="update-vehiculos.php?id=<?php echo $vehiculo['id']; ?>"
                                                            class="action-icon"> <i
                                                                class="mdi mdi-square-edit-outline"></i></a>
                                                        <a href="#" class="action-icon" onclick="eliminarVehiculo(<?php echo $vehiculo['id']; ?>)"> <i class="mdi mdi-delete"></i></a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col -->
                    </div>
                    
                    <!--tabla de usuarios-->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h4 class="header-title">Administrar usuarios</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-sm-5">
                                            <a href="add-usuarios.php" class="btn btn-success mb-2"><i
                                                    class="mdi mdi-plus-circle me-2"></i> Agregar usuarios</a>
                                        </div>
                                        <div class="col-sm-7">
                                            <div class="text-sm-end">
                                                <button type="button" class="btn btn-light mb-2 me-1">Importar</button>
                                                <button type="button" class="btn btn-light mb-2">Exportar</button>
                                            </div>
                                        </div><!-- end col-->
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-centered table-striped dt-responsive nowrap w-100"
                                            id="users-datatable">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20px;">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="customCheck1">
                                                            <label class="form-check-label"
                                                                for="customCheck1">&nbsp;</label>
                                                        </div>
                                                    </th>
                                                    <th>ID</th>
                                                    <th>Nombres</th>
                                                    <th>Apellidos</th>
                                                    <th>Email</th>
                                                    <th>Teléfono</th>
                                                    <th>Rol</th>
                                                    <th style="width: 75px;">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($usuarios as $usuario): ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                id="customCheck2">
                                                            <label class="form-check-label"
                                                                for="customCheck2">&nbsp;</label>
                                                        </div>
                                                    </td>
                                                    <td class="table-user">
                                                        <img src="assets/images/users/avatar-4.jpg" alt="table-user"
                                                            class="me-2 rounded-circle">
                                                        <a href="javascript:void(0);"
                                                            class="text-body fw-semibold"><?php echo $usuario['id']; ?></a>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($usuario['first_name']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($usuario['last_name']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($usuario['email']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($usuario['phone']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo htmlspecialchars($usuario['rol_nombre']); ?>
                                                    </td>
                                                    <td>
                                                        <a href="update-usuarios.php?id=<?php echo $usuario['id']; ?>"
                                                            class="action-icon"> <i
                                                                class="mdi mdi-square-edit-outline"></i></a>
                                                        <a href="#" class="action-icon" onclick="eliminarUsuario(<?php echo $usuario['id']; ?>)"> <i class="mdi mdi-delete"></i></a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col -->
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

    <!-- Modal de confirmación para eliminar -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor js -->
    <?php
    include("includes/js.php");
    ?>

    <script>
        let deleteId = null;
        let deleteType = null;

        function eliminarVehiculo(id) {
            deleteId = id;
            deleteType = 'vehiculo';
            document.getElementById('confirmModalLabel').textContent = 'Eliminar vehículo';
            document.querySelector('.modal-body').textContent = '¿Estás seguro de que deseas eliminar este vehículo? Esta acción no se puede deshacer.';
            var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        function eliminarUsuario(id) {
            deleteId = id;
            deleteType = 'usuario';
            document.getElementById('confirmModalLabel').textContent = 'Eliminar usuario';
            document.querySelector('.modal-body').textContent = '¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.';
            var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            modal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteType === 'vehiculo') {
                // Crear formulario para eliminar vehículo
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'action/delete-vehiculos.php';
                form.style.display = 'none';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = deleteId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            } else if (deleteType === 'usuario') {
                // Crear formulario para eliminar usuario
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'action/delete-usuarios.php';
                form.style.display = 'none';
                
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'id';
                input.value = deleteId;
                
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });
    </script>
</body>
</html>