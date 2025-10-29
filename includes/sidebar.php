<!-- ========== Left Sidebar Start ========== -->
<div class="leftside-menu">
    <!-- Brand Logo Light -->
    <a href="index.php" class="logo logo-light mb-4">
        <span class="logo-lg text-white my-3">
            <h3>SYSMAINT</h3>
        </span>
        <span class="logo-sm text-white ">
            <h3>S</h3>
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="index.php" class="logo logo-dark ">
        <span class="logo-lg">
            <img src="assets/images/logo-dark.png" alt="dark logo" />
        </span>
        <span class="logo-sm">
            <img src="assets/images/logo-dark-sm.png" alt="small logo" />
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a href="mi-perfil.php">
                <img src="<?php echo !empty($_SESSION['user_imagen']) ? $_SESSION['user_imagen'] : 'assets/images/uploads/users/user-default.png'; ?>" 
                     alt="user-image" height="42" class="rounded-circle shadow-sm" />
                <span class="leftbar-user-name mt-2"><?php echo htmlspecialchars($_SESSION['user_nombre'] ?? 'Usuario'); ?></span>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title">Menú</li>

            <li class="side-nav-item">
                <a href="index.php" class="side-nav-link">
                    <i class="uil-home-alt"></i>
                    <span> Inicio </span>
                </a>
            </li>

            <?php if (isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'admin'): ?>
            <li class="side-nav-item">
                <a href="usuarios.php" class="side-nav-link">
                    <i class="ri-user-settings-line"></i>
                    <span> Usuarios </span>
                </a>
            </li>
            <?php endif; ?>

            <li class="side-nav-title">Gestión de Flota</li>

            <li class="side-nav-item">
                <a href="vehiculos.php" class="side-nav-link">
                    <i class="ri-car-line"></i>
                    <span> Vehículos </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="mantenimientos.php" class="side-nav-link">
                    <i class="ri-tools-line"></i>
                    <span> Mantenimientos </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#menuVehiculos" aria-expanded="false" aria-controls="menuVehiculos" class="side-nav-link">
                    <i class="ri-settings-3-line"></i>
                    <span> Acciones Rápidas </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="menuVehiculos">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="add-vehiculos.php">
                                <i class="ri-add-circle-line"></i> Agregar Vehículo
                            </a>
                        </li>
                        <li>
                            <a href="add-mantenimientos.php">
                                <i class="ri-tools-fill"></i> Registrar Mantenimiento
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>
<!-- ========== Left Sidebar End ========== -->