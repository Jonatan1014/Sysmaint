<?php
    session_start();
    if(isset($_SESSION['user'])){//valido si existe una sesion
        $nameUser = $_SESSION['user'];
        $id_device = $_SESSION['id_devices'];
        
    }else{

        header('Location: login.php');      
    }

?>
<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8" />
    <title>Equipos | Sysmaint</title>
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

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-5">
                                            <!-- Product image -->
                                            <a href="javascript: void(0);" class="text-center d-block mb-4">
                                                <img src="assets/images/equipos/aire.png" class="img-fluid"
                                                    style="max-width: 280px;" alt="Product-img" />
                                            </a>

                                        </div> <!-- end col -->
                                        <div class="col-lg-7">
                                            <!-- Product title -->
                                            <h3 class="mt-0"><?php echo $id_device['name']; ?> <a
                                                    href="javascript: void(0);" class="text-muted"></a> </h3>
                                            <p class="mb-1">Numero de registro: <?php echo $id_device['id']; ?>
                                            <p class="mb-1">Fecha de compra: <?php echo $id_device['purchase_date']; ?>
                                            </p>
                                            <p class="mb-1">Fecha de instalación:
                                                <?php echo $id_device['installation_date']; ?></p>
                                            <!-- Product stock -->
                                            <div class="mt-3">
                                                <h4><span
                                                        class="badge badge-success-lighten"><?php echo $id_device['physical_state']; ?></span>
                                                </h4>
                                            </div>


                                            <!-- Product description -->
                                            <div class="mt-4">
                                                <h6 class="font-14">Descripción de estado:</h6>
                                                <p><?php echo $id_device['status_description']; ?></p>
                                            </div>
                                        </div> <!-- end col -->

                                        <div class="col-lg-5">
                                            <div class="mt-4">
                                                <h6 class="font-14">Marca:</h6>
                                                <p><?php echo $id_device['brand']; ?></p>
                                            </div>

                                            <div class="mt-4">
                                                <h6 class="font-14">Modelo:</h6>
                                                <p><?php echo $id_device['model']; ?></p>
                                            </div>

                                            <div class="mt-4">
                                                <h6 class="font-14">Número de serie:</h6>
                                                <p><?php echo $id_device['serial_number']; ?></p>
                                            </div>

                                            <!-- Product information -->
                                            <div class="mt-4">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <h6 class="font-14">Espesificaciones tecnicas:</h6>
                                                        <p class="text-sm lh-150">
                                                            <?php echo $id_device['technical_specifications']; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-5">

                                            <div class="mt-4">
                                                <h6 class="font-14">Ubicación:</h6>
                                                <p><?php echo $id_device['location']; ?></p>
                                            </div>

                                            <div class="mt-4">
                                                <h6 class="font-14">Garantia</h6>
                                                <p><?php echo $id_device['garantia']; ?></p>
                                            </div>

                                            <div class="mt-4">
                                                <h6 class="font-14">Precio:</h6>
                                                <p><?php echo $id_device['current_value']; ?></p>
                                            </div>
                                            <div class="mt-4">
                                                <h6 class="font-14">Accesorios</h6>
                                                <p><?php echo $id_device['accessories']; ?></p>
                                            </div>


                                        </div>
                                    </div> <!-- end row-->
                                </div> <!-- end card-body-->
                            </div> <!-- end card-->
                        </div> <!-- end col-->
                    </div>
                    <!-- end row-->


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