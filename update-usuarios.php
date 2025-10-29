<?php
session_start();
require_once 'includes/Class-usuario.php';

// Verificar si el usuario está logueado
$Usuario_class = new Usuario();
if (!$Usuario_class->usuarioLogueado()) {
    header('Location: login.php');
    exit;
}

// Verificar si se pasó un ID de usuario para actualizar
if (!isset($_GET['id']) && !isset($_POST['id_usuario'])) {
    $_SESSION['error'] = 'ID de usuario no proporcionado.';
    header('Location: admin.php');
    exit;
}

$id_usuario = isset($_GET['id']) ? $_GET['id'] : $_POST['id_usuario'];

// Verificar permisos (opcional: solo el mismo usuario o admin pueden actualizar)
// $usuario_logueado = $_SESSION['user_rol_nombre'];
// if ($usuario_logueado['id'] != $id_usuario && $usuario_logueado['rol'] != 'admin' && $usuario_logueado['rol'] != 'root') {
//     $_SESSION['error'] = 'No tienes permisos para actualizar este usuario.';
//     header('Location: admin.php');
//     exit;
// }

// Obtener datos del usuario a actualizar
$datosUser = $Usuario_class->obtenerUsuarioPorId($id_usuario);

if (!$datosUser) {
    $_SESSION['error'] = 'Usuario no encontrado.';
    header('Location: admin.php');
    exit;
}

$nameUser = $_SESSION['user_rol_nombre'] . ' ' . $_SESSION['user_rol_apellido'];

// Obtener roles disponibles
$roles = $Usuario_class->obtenerRoles();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Actualizar Usuario | Sysmaint</title>
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
                                    <h4 class="header-title">Actualizar usuario</h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <form method="POST" action="action/update-usuarios.php">
                                                <!-- Campo oculto para el ID del usuario -->
                                                <input type="hidden" name="id" value="<?php echo $datosUser['id']; ?>">

                                                <div class="mb-3">
                                                    <label for="user-code-cc" class="form-label">Cédula o Código</label>
                                                    <input type="text" id="user-code-cc" class="form-control"
                                                        name="code_cc" value="<?php echo htmlspecialchars($datosUser['code_cc']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-first-name" class="form-label">Nombres</label>
                                                    <input type="text" id="user-first-name" class="form-control"
                                                        name="first_name" value="<?php echo htmlspecialchars($datosUser['first_name']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-last-name" class="form-label">Apellidos</label>
                                                    <input type="text" id="user-last-name" class="form-control"
                                                        name="last_name" value="<?php echo htmlspecialchars($datosUser['last_name']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-email" class="form-label">Correo electrónico</label>
                                                    <input type="email" id="user-email" class="form-control"
                                                        name="email" value="<?php echo htmlspecialchars($datosUser['email']); ?>" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="user-password" class="form-label">Nueva Contraseña (dejar en blanco para mantener la actual)</label>
                                                    <input type="password" id="user-password" class="form-control"
                                                        name="password">
                                                </div>

                                        </div> <!-- end col -->

                                        <div class="col-lg-6">

                                            <div class="mb-3">
                                                <label for="user-phone" class="form-label">Teléfono</label>
                                                <input type="tel" id="user-phone" class="form-control"
                                                    name="phone" value="<?php echo htmlspecialchars($datosUser['phone']); ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-address" class="form-label">Dirección</label>
                                                <textarea id="user-address" class="form-control" name="direccion" rows="3"><?php echo htmlspecialchars($datosUser['direccion']); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="user-rol" class="form-label">Rol</label>
                                                <select class="form-select" id="user-rol" name="rol" required>
                                                    <option value="">Seleccionar rol</option>
                                                    <?php foreach ($roles as $rol): ?>
                                                        <option value="<?php echo htmlspecialchars($rol['nombre']); ?>" 
                                                            <?php echo ($datosUser['rol'] == $rol['nombre']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($rol['nombre']); ?>
                                                            <?php if ($rol['descripcion']): ?>
                                                                (<?php echo htmlspecialchars($rol['descripcion']); ?>)
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="user-estado" name="estado" 
                                                    <?php echo ($datosUser['estado'] == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="user-estado">Activo</label>
                                            </div>

                                        </div>
                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary" name="submit">Actualizar Datos</button>
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
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

    <!-- Vendor js -->
    <?php
    include("includes/js.php");
    ?>

</body>

</html>