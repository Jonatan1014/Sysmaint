<?php
session_start();
require_once 'includes/Class-usuario.php';

// Verificar si el usuario está logueado
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

// Obtener roles disponibles
$roles = $Usuario_class->obtenerRoles();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Añadir usuarios | Sysmaint</title>
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

                    <!-- titulo pagina-->
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
                                <h4 class="page-title">Usuarios</h4>
                            </div>
                        </div>
                    </div>

                    <!-- formulario -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Agregar usuarios</h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <form method="POST" action="action/add-usuarios.php">
                                                <div class="mb-3">
                                                    <label for="user-code-cc" class="form-label">Cédula o Código</label>
                                                    <input type="text" id="user-code-cc" class="form-control"
                                                        name="code_cc" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-first-name" class="form-label">Nombres</label>
                                                    <input type="text" id="user-first-name" class="form-control"
                                                        name="first_name" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-last-name" class="form-label">Apellidos</label>
                                                    <input type="text" id="user-last-name" class="form-control"
                                                        name="last_name" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-email" class="form-label">Correo electrónico</label>
                                                    <input type="email" id="user-email" class="form-control"
                                                        name="email" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-password" class="form-label">Contraseña</label>
                                                    <input type="password" id="user-password" class="form-control"
                                                        name="password" required>
                                                </div>

                                        </div> <!-- end col -->

                                        <div class="col-lg-6">

                                            <div class="mb-3">
                                                <label for="user-phone" class="form-label">Teléfono</label>
                                                <input type="tel" id="user-phone" class="form-control"
                                                    name="phone">
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-address" class="form-label">Dirección</label>
                                                <textarea id="user-address" class="form-control" name="direccion" rows="3"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-rol" class="form-label">Rol</label>
                                                <select class="form-select" id="user-rol" name="rol" required>
                                                    <option value="">Seleccionar rol</option>
                                                    <?php foreach ($roles as $rol): ?>
                                                        <option value="<?php echo htmlspecialchars($rol['nombre']); ?>">
                                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                                            <?php if ($rol['descripcion']): ?>
                                                                (<?php echo htmlspecialchars($rol['descripcion']); ?>)
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="user-estado" name="estado" checked>
                                                <label class="form-check-label" for="user-estado">Activo</label>
                                            </div>

                                            <!-- Campo oculto para la empresa -->
                                            <input type="hidden" name="id_empresa" value="<?php echo $_SESSION['user_empresa_id']; ?>">

                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary" name="submit">Enviar</button>
                                            <a href="admin.php" class="btn btn-secondary">Cancelar</a>
                                        </div>
                                        </form>

                                    </div>
                                    <!-- end row-->
                                </div> <!-- end card-body -->
                            </div> <!-- end card -->
                        </div><!-- end col -->
                    </div><!-- end row -->
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
        <!-- End Page Content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <?php
    include("includes/js.php");
    ?>

</body>

</html>