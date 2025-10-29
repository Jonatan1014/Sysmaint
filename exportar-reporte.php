<?php
// exportar-reporte.php - Exportar reportes en diferentes formatos
session_start();
require_once 'includes/Class-usuario.php';
require_once 'includes/Class-vehiculos.php';
require_once 'includes/Class-mantenimientos.php';

$Usuario_class = new Usuario();

if (!$Usuario_class->usuarioLogueado()) {
    header("Location: login.php");
    exit;
}

$id_empresa = $_SESSION['user_empresa_id'] ?? null;
$formato = $_GET['formato'] ?? 'csv'; // csv, excel, pdf

$Vehiculo_class = new Vehiculo();
$Mantenimiento_class = new Mantenimiento();

// Obtener datos
$dashboard = $Mantenimiento_class->obtenerDashboardCompleto($id_empresa);
$estadisticas_vehiculos = $Vehiculo_class->obtenerEstadisticas($id_empresa);

if ($formato === 'csv' || $formato === 'excel') {
    // Configurar headers para descarga
    $filename = 'reporte_mantenimiento_' . date('Y-m-d_His') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Crear archivo CSV
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Título del reporte
    fputcsv($output, ['REPORTE DE MANTENIMIENTO']);
    fputcsv($output, ['Fecha de generación: ' . date('d/m/Y H:i:s')]);
    fputcsv($output, []);
    
    // Estadísticas Generales
    fputcsv($output, ['ESTADÍSTICAS GENERALES']);
    fputcsv($output, ['Total de Vehículos', $estadisticas_vehiculos['total_vehiculos']]);
    fputcsv($output, ['Total de Mantenimientos', $dashboard['estadisticas_generales']['total_mantenimientos']]);
    fputcsv($output, ['Próximos Mantenimientos (30 días)', $dashboard['estadisticas_generales']['proximos_30_dias']]);
    fputcsv($output, ['Costo Total', '$' . number_format($dashboard['estadisticas_generales']['costo_total'], 2)]);
    fputcsv($output, []);
    
    // Vehículos con más mantenimientos
    fputcsv($output, ['VEHÍCULOS CON MÁS MANTENIMIENTOS']);
    fputcsv($output, ['Placa', 'Categoría', 'Total Mantenimientos', 'Costo Total', 'Costo Promedio', 'Último Mantenimiento']);
    foreach ($dashboard['vehiculos_mas_mantenimiento'] as $vehiculo) {
        fputcsv($output, [
            $vehiculo['placa'],
            $vehiculo['categoria'],
            $vehiculo['total_mantenimientos'],
            '$' . number_format($vehiculo['costo_total_acumulado'], 2),
            '$' . number_format($vehiculo['costo_promedio'], 2),
            date('d/m/Y', strtotime($vehiculo['ultimo_mantenimiento']))
        ]);
    }
    fputcsv($output, []);
    
    // Materiales más comprados
    fputcsv($output, ['MATERIALES MÁS COMPRADOS']);
    fputcsv($output, ['Material', 'Cantidad Total', 'Unidad', 'Veces Usado', 'Costo Total']);
    foreach ($dashboard['materiales_mas_comprados'] as $material) {
        fputcsv($output, [
            $material['nombre_material'],
            number_format($material['cantidad_total'], 2),
            $material['unidad_medida'],
            $material['veces_usado'],
            '$' . number_format($material['costo_total'], 2)
        ]);
    }
    fputcsv($output, []);
    
    // Costos mensuales
    fputcsv($output, ['COSTOS MENSUALES']);
    fputcsv($output, ['Mes', 'Cantidad Mantenimientos', 'Mano de Obra', 'Materiales', 'Total']);
    foreach ($dashboard['costo_mensual'] as $mes) {
        fputcsv($output, [
            $mes['mes'],
            $mes['cantidad_mantenimientos'],
            '$' . number_format($mes['total_mano_obra'], 2),
            '$' . number_format($mes['total_materiales'], 2),
            '$' . number_format($mes['total_costo'], 2)
        ]);
    }
    fputcsv($output, []);
    
    // Próximos mantenimientos
    fputcsv($output, ['PRÓXIMOS MANTENIMIENTOS (30 DÍAS)']);
    fputcsv($output, ['Vehículo', 'Categoría', 'Último Mantenimiento', 'Próximo Mantenimiento', 'Días Restantes', 'Estado']);
    foreach ($dashboard['proximos_mantenimientos'] as $proximo) {
        $dias_restantes = ceil((strtotime($proximo['proximo_mantenimiento']) - time()) / 86400);
        fputcsv($output, [
            $proximo['vehiculo_placa'],
            $proximo['vehiculo_categoria'],
            date('d/m/Y', strtotime($proximo['fecha_mantenimiento'])),
            date('d/m/Y', strtotime($proximo['proximo_mantenimiento'])),
            $dias_restantes . ' días',
            $proximo['estado_nombre']
        ]);
    }
    fputcsv($output, []);
    
    // Estados de mantenimiento
    fputcsv($output, ['DISTRIBUCIÓN POR ESTADO']);
    fputcsv($output, ['Estado', 'Cantidad', 'Porcentaje']);
    $total_estados = array_sum(array_column($dashboard['estadisticas_generales']['por_estado'], 'cantidad'));
    foreach ($dashboard['estadisticas_generales']['por_estado'] as $estado) {
        $porcentaje = $total_estados > 0 ? ($estado['cantidad'] / $total_estados * 100) : 0;
        fputcsv($output, [
            $estado['nombre'],
            $estado['cantidad'],
            number_format($porcentaje, 1) . '%'
        ]);
    }
    fputcsv($output, []);
    
    // Análisis de costos por vehículo
    fputcsv($output, ['ANÁLISIS DE COSTOS POR VEHÍCULO']);
    fputcsv($output, ['Placa', 'Categoría', 'Total Mantenimientos', 'Mano de Obra', 'Materiales', 'Costo Total']);
    foreach ($dashboard['costos_por_vehiculo'] as $vehiculo) {
        fputcsv($output, [
            $vehiculo['placa'],
            $vehiculo['categoria'],
            $vehiculo['total_mantenimientos'],
            '$' . number_format($vehiculo['total_mano_obra'] ?? 0, 2),
            '$' . number_format($vehiculo['total_materiales'] ?? 0, 2),
            '$' . number_format($vehiculo['costo_total'] ?? 0, 2)
        ]);
    }
    
    fclose($output);
    exit;
}

// Si el formato no es válido, redirigir
header("Location: index.php");
exit;
?>
