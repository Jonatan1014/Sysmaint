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
    $_SESSION['error'] = 'ID de vehículo no proporcionado.';
    header('Location: vehiculos.php');
    exit;
}

$id_vehiculo = $_GET['id'];

$Vehiculo_class = new Vehiculo();
$Mantenimiento_class = new Mantenimiento();

// Obtener datos del vehículo
$vehiculo = $Vehiculo_class->obtenerVehiculoPorId($id_vehiculo);

if (!$vehiculo) {
    $_SESSION['error'] = 'Vehículo no encontrado.';
    header('Location: vehiculos.php');
    exit;
}

// Obtener historial de mantenimientos
$mantenimientos = $Mantenimiento_class->obtenerMantenimientosPorVehiculo($id_vehiculo);
$costo_total = $Mantenimiento_class->obtenerCostoTotalPorVehiculo($id_vehiculo);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Detalle de Vehículo | Sysmaint</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="shortcut icon" href="assets/images/favicon.ico" />
    <script src="assets/js/hyper-config.js"></script>
    <link href="assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    
    <style>
        /* Estilos personalizados para el carrusel */
        #carouselVehiculo {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border-radius: 8px;
            overflow: hidden;
        }
        
        #carouselVehiculo .carousel-control-prev,
        #carouselVehiculo .carousel-control-next {
            width: 10%;
            background: rgba(0,0,0,0.3);
        }
        
        #carouselVehiculo .carousel-control-prev:hover,
        #carouselVehiculo .carousel-control-next:hover {
            background: rgba(0,0,0,0.5);
        }
        
        #carouselVehiculo .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 4px;
        }
        
        #carouselVehiculo .carousel-item img {
            transition: transform 0.3s ease;
        }
        
        #carouselVehiculo .carousel-item:hover img {
            transform: scale(1.05);
        }
        
        .sin-imagenes-icon {
            transition: transform 0.3s ease;
        }
        
        .sin-imagenes-icon:hover {
            transform: scale(1.1);
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
                                <div class="page-title-right">
                                    <a href="vehiculos.php" class="btn btn-secondary btn-sm">
                                        <i class="mdi mdi-arrow-left me-1"></i> Volver
                                    </a>
                                </div>
                                <h4 class="page-title">Detalle del Vehículo</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Información del vehículo -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-4 text-center">
                                            <!-- Carrusel de imágenes del vehículo -->
                                            <div class="mb-3">
                                                <?php 
                                                $imagenes = json_decode($vehiculo['imagenes'], true);
                                                if ($imagenes && count($imagenes) > 0):
                                                ?>
                                                    <div id="carouselVehiculo" class="carousel slide" data-bs-ride="carousel">
                                                        <!-- Indicadores -->
                                                        <?php if (count($imagenes) > 1): ?>
                                                        <div class="carousel-indicators">
                                                            <?php foreach ($imagenes as $index => $imagen): ?>
                                                            <button type="button" 
                                                                    data-bs-target="#carouselVehiculo" 
                                                                    data-bs-slide-to="<?php echo $index; ?>" 
                                                                    <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?>
                                                                    aria-label="Imagen <?php echo $index + 1; ?>">
                                                            </button>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Slides -->
                                                        <div class="carousel-inner rounded">
                                                            <?php foreach ($imagenes as $index => $imagen): ?>
                                                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                                                <img src="<?php echo htmlspecialchars($imagen); ?>" 
                                                                     class="d-block w-100" 
                                                                     alt="Imagen <?php echo $index + 1; ?> del vehículo"
                                                                     style="height: 300px; object-fit: cover;">
                                                                <div class="carousel-caption d-none d-md-block" 
                                                                     style="background: rgba(0,0,0,0.5); border-radius: 5px; padding: 5px 10px;">
                                                                    <p class="mb-0">Imagen <?php echo $index + 1; ?> de <?php echo count($imagenes); ?></p>
                                                                </div>
                                                            </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                        
                                                        <!-- Controles -->
                                                        <?php if (count($imagenes) > 1): ?>
                                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselVehiculo" data-bs-slide="prev">
                                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                            <span class="visually-hidden">Anterior</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselVehiculo" data-bs-slide="next">
                                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                            <span class="visually-hidden">Siguiente</span>
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="bg-light rounded p-5 sin-imagenes-icon">
                                                        <i class="ri-car-line" style="font-size: 120px; opacity: 0.3;"></i>
                                                        <p class="text-muted mt-3 mb-0">Sin imágenes</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <h3 class="mb-1"><?php echo htmlspecialchars($vehiculo['placa']); ?></h3>
                                            <p class="text-muted">
                                                <span class="badge" style="background-color: <?php echo htmlspecialchars($vehiculo['color']); ?>; font-size: 14px;">
                                                    <?php echo htmlspecialchars($vehiculo['color']); ?>
                                                </span>
                                            </p>

                                            <div class="d-grid gap-2">
                                                <a href="update-vehiculos.php?id=<?php echo $vehiculo['id']; ?>" class="btn btn-warning">
                                                    <i class="mdi mdi-pencil me-1"></i> Editar Vehículo
                                                </a>
                                                <a href="add-mantenimientos.php?id_vehiculo=<?php echo $vehiculo['id']; ?>" class="btn btn-success">
                                                    <i class="mdi mdi-wrench me-1"></i> Nuevo Mantenimiento
                                                </a>
                                            </div>
                                        </div>

                                        <div class="col-lg-8">
                                            <h4 class="mb-3">Información General</h4>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold">Categoría:</label>
                                                        <p><?php echo htmlspecialchars($vehiculo['categoria_nombre'] ?? 'No especificada'); ?></p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="fw-bold">Conductor Asignado:</label>
                                                        <p>
                                                            <?php 
                                                            if ($vehiculo['conductor_nombre']) {
                                                                echo htmlspecialchars($vehiculo['conductor_nombre'] . ' ' . $vehiculo['conductor_apellido']);
                                                                echo '<br><small class="text-muted">' . htmlspecialchars($vehiculo['conductor_email']) . '</small>';
                                                            } else {
                                                                echo '<span class="text-muted">Sin asignar</span>';
                                                            }
                                                            ?>
                                                        </p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="fw-bold">Estado:</label>
                                                        <p>
                                                            <?php if ($vehiculo['estado'] == 1): ?>
                                                                <span class="badge bg-success">Activo</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Inactivo</span>
                                                            <?php endif; ?>
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="fw-bold">Descripción:</label>
                                                        <p><?php echo $vehiculo['descripcion'] ? nl2br(htmlspecialchars($vehiculo['descripcion'])) : '<span class="text-muted">Sin descripción</span>'; ?></p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="fw-bold">Registrado:</label>
                                                        <p><?php echo date('d/m/Y H:i', strtotime($vehiculo['created_at'])); ?></p>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="fw-bold">Última actualización:</label>
                                                        <p><?php echo date('d/m/Y H:i', strtotime($vehiculo['updated_at'])); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas de mantenimiento -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0">Total Mantenimientos</h5>
                                            <h3 class="my-2"><?php echo count($mantenimientos); ?></h3>
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

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0">Costo Total</h5>
                                            <h3 class="my-2">$<?php echo number_format($costo_total, 2); ?></h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-money-dollar-circle-line" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h5 class="text-muted fw-normal mt-0">Último Mantenimiento</h5>
                                            <h3 class="my-2">
                                                <?php 
                                                if (count($mantenimientos) > 0) {
                                                    echo date('d/m/Y', strtotime($mantenimientos[0]['fecha_mantenimiento']));
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </h3>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-end">
                                                <i class="ri-calendar-check-line" style="font-size: 48px; opacity: 0.3;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de mantenimientos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="header-title mb-3">Historial de Mantenimientos</h4>

                                    <?php if (count($mantenimientos) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-centered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Técnico</th>
                                                        <th>Estado</th>
                                                        <th>Mano de Obra</th>
                                                        <th>Materiales</th>
                                                        <th>Costo Total</th>
                                                        <th>Items</th>
                                                        <th>Observaciones</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($mantenimientos as $mant): ?>
                                                    <tr>
                                                        <td><?php echo date('d/m/Y', strtotime($mant['fecha_mantenimiento'])); ?></td>
                                                        <td><?php echo htmlspecialchars($mant['tecnico_nombre'] . ' ' . $mant['tecnico_apellido']); ?></td>
                                                        <td>
                                                            <span class="badge" style="background-color: <?php echo $mant['estado_color']; ?>;">
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
                                                            <span class="badge bg-secondary">
                                                                <?php echo $mant['total_materiales'] ?? 0; ?> items
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php 
                                                            $obs = $mant['observaciones'];
                                                            echo $obs ? (strlen($obs) > 50 ? substr($obs, 0, 50) . '...' : $obs) : '-';
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="mantenimiento-detalle.php?id=<?php echo $mant['id']; ?>" 
                                                               class="btn btn-sm btn-info">
                                                                <i class="mdi mdi-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="ri-tools-line" style="font-size: 72px; opacity: 0.2;"></i>
                                            <p class="text-muted mt-3">No hay mantenimientos registrados para este vehículo.</p>
                                            <a href="add-mantenimientos.php?id_vehiculo=<?php echo $vehiculo['id']; ?>" class="btn btn-primary">
                                                <i class="mdi mdi-plus me-1"></i> Registrar Primer Mantenimiento
                                            </a>
                                        </div>
                                    <?php endif; ?>
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
</body>
</html>
