<?php
    session_start();
    if(isset($_SESSION['user'])){
        
        $nameUser = $_SESSION['user'];
    }else{
        
        header('Location: login.php');      
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Añadir equipos | Sysmaint</title>
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
                                <h4 class="page-title">Equipos</h4>
                            </div>
                        </div>
                    </div>

                    <!-- formulario -->
                    <div class="row ">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title">Agregar equipos</h4>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <form method="POST" action="api-device\sendDevice.php">

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Nombre del
                                                        dispositivo</label>
                                                    <input type="text" id="simpleinput" class="form-control"
                                                        name="device-name">
                                                </div>

                                                <div class=" mb-3">
                                                    <label for="simpleinput" class="form-label">Marca del
                                                        dispositivo</label>
                                                    <input type="text" id="simpleinput" class="form-control"
                                                        name="device-brand">
                                                </div>

                                                <div class=" mb-3">
                                                    <label for="simpleinput" class="form-label">Modelo del
                                                        dispositivo</label>
                                                    <input type="text" id="simpleinput" class="form-control"
                                                        name="device-model">
                                                </div>

                                                <div class=" mb-3">
                                                    <label for="simpleinput" class="form-label">Numero de serie</label>
                                                    <input type="text" id="simpleinput" class="form-control"
                                                        name="device-serial-number">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-date" class="form-label">Fecha de compra</label>
                                                    <input class="form-control" id="example-date" type="date"
                                                        name="device-purchase-date">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="simpleinput" class="form-label">Ubicación del
                                                        dispositivo</label>
                                                    <input type="text" id="simpleinput" class="form-control"
                                                        name="device-location">
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-select" class="form-label">Estado del
                                                        dispositivo</label>
                                                    <select class="form-select" id="example-select"
                                                        name="device-status">
                                                        <option>Activo</option>
                                                        <option>Inactivo</option>
                                                        <option>Mantenimiento</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="example-textarea" class="form-label">Descripción del
                                                        dispositivo</label>
                                                    <textarea class="form-control" id="example-textarea" rows="3"
                                                        name="device-status-description"></textarea>
                                                </div>
                                        </div> <!-- end col -->

                                        <div class="col-lg-6">

                                            <div class="mb-3">
                                                <label for="example-textarea" class="form-label">Espesificaciones
                                                    tecnicas</label>
                                                <textarea class="form-control" id="example-textarea" rows="5"
                                                    name="device-specifications"></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="example-date" class="form-label">Fecha de
                                                    instalacion</label>
                                                <input class="form-control" id="example-date" type="date"
                                                    name="device-installation-date">
                                            </div>

                                            <div class="mb-3">
                                                <label for="example-select" class="form-label">Garantia del
                                                    dispositivo</label>
                                                <select class="form-select" id="example-select" name="device-garantia">
                                                    <option>Si</option>
                                                    <option>No</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="example-select" class="form-label">Tiene Accesorios</label>
                                                <select class="form-select" id="example-select"
                                                    name="device-accessories">
                                                    <option>Si</option>
                                                    <option>No</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Valor del
                                                    dispositivo</label>
                                                <input type="number" id="simpleinput" class="form-control" value="0"
                                                    min="1" name="current_value">
                                            </div>



                                            <div class="mb-3">
                                                <label for="example-fileinput" class="form-label">Imagen del
                                                    dispositivo</label>
                                                <input type="file" id="example-fileinput" class="form-control"
                                                    name="device-image">
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="mb-3">
                                            <button type="submit" class="btn btn-primary" name="submit">Enviar</button>
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