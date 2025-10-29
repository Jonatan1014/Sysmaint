# Dashboard de Mantenimiento - Documentación

## 📊 Descripción General

El dashboard de mantenimiento proporciona una vista completa y visual de todas las operaciones de mantenimiento de la flota de vehículos, incluyendo estadísticas, gráficas interactivas y análisis detallados.

## ✨ Características Principales

### 1. **Estadísticas Principales** (Tarjetas superiores)

#### Total de Vehículos
- Muestra el número total de vehículos activos en el sistema
- Icono: Camión (truck)
- Color: Azul primario

#### Total de Mantenimientos
- Cantidad total de mantenimientos realizados
- Icono: Herramienta (tool)
- Color: Verde success

#### Próximos Mantenimientos (30 días)
- Número de mantenimientos programados para los próximos 30 días
- Icono: Reloj (clock)
- Color: Amarillo warning

#### Costo Total
- Suma total de todos los costos de mantenimiento
- Incluye: mano de obra + materiales
- Icono: Dólar (dollar-sign)
- Color: Rojo danger

### 2. **Gráficas Interactivas**

#### Gráfica de Costos Mensuales (Línea)
- **Tipo:** Gráfica de líneas múltiples
- **Datos mostrados:**
  - Línea azul: Mano de obra
  - Línea roja: Materiales
  - Línea verde: Total
- **Período:** Últimos 12 meses
- **Características:**
  - Interactiva (hover para ver detalles)
  - Tooltips con valores formateados en pesos
  - Fill area para mejor visualización de tendencias

#### Gráfica de Estados (Donut)
- **Tipo:** Gráfica de dona (doughnut)
- **Datos mostrados:**
  - Distribución de mantenimientos por estado
  - Colores según el estado configurado
- **Características:**
  - Muestra porcentaje y cantidad
  - Leyenda en la parte inferior
  - Colores personalizados por estado

#### Gráfica de Costos por Vehículo (Barras Apiladas)
- **Tipo:** Gráfica de barras apiladas
- **Datos mostrados:**
  - Top 10 vehículos con más costos
  - Separación entre mano de obra y materiales
- **Características:**
  - Barras apiladas para ver composición de costos
  - Tooltip con desglose detallado
  - Footer con total por vehículo

### 3. **Tablas de Análisis**

#### Vehículos con Más Mantenimientos
**Columnas:**
- Placa (enlace al detalle del vehículo)
- Categoría
- Cantidad de mantenimientos (badge azul)
- Costo total acumulado
- Costo promedio por mantenimiento

**Uso:** Identificar vehículos que requieren más atención y analizar costos

#### Materiales Más Comprados
**Columnas:**
- Nombre del material
- Cantidad total (con unidad de medida)
- Veces usado (badge info)
- Costo total

**Uso:** Optimizar inventario y compras basado en consumo histórico

#### Próximos Mantenimientos
**Columnas:**
- Vehículo (enlace al detalle)
- Categoría
- Último mantenimiento realizado
- Próximo mantenimiento programado
- Días restantes (badge con código de colores)
- Estado actual

**Código de colores (días restantes):**
- 🔴 Rojo (danger): ≤ 7 días - Urgente
- 🟡 Amarillo (warning): ≤ 15 días - Próximo
- 🟢 Verde (success): > 15 días - Programado

**Uso:** Planificar mantenimientos preventivos y evitar atrasos

#### Análisis de Costos por Vehículo
**Datos visualizados:**
- Todos los vehículos con mantenimientos
- Desglose completo de costos
- Comparativa visual

## 📥 Funciones de Exportación

### Exportar como CSV
- **Formato:** Comma-Separated Values
- **Codificación:** UTF-8 con BOM
- **Incluye:**
  - Estadísticas generales
  - Vehículos con más mantenimientos
  - Materiales más comprados
  - Costos mensuales
  - Próximos mantenimientos
  - Distribución por estados
  - Análisis completo por vehículo

**Uso:** Ideal para importar a Excel, Google Sheets, o análisis con otras herramientas

### Exportar como TXT
- **Formato:** Texto plano
- **Estructura:** Formato legible con separadores
- **Incluye:**
  - Estadísticas principales
  - Top vehículos y materiales
  - Totales y promedios

**Uso:** Reportes rápidos, emails, o documentación

### Imprimir
- **Formato:** Optimizado para impresión
- **Características:**
  - Oculta elementos de navegación
  - Ajusta márgenes automáticamente
  - Respeta saltos de página en tarjetas

## 🔍 Análisis y Métricas

### Razones de Mantenimiento
El dashboard identifica automáticamente:
1. **Vehículos con más mantenimientos:**
   - Permite identificar vehículos problemáticos
   - Útil para decisiones de reemplazo

2. **Análisis de costos:**
   - Compara mano de obra vs materiales
   - Identifica tendencias de gasto

3. **Materiales más usados:**
   - Optimiza inventario
   - Identifica oportunidades de compra al por mayor

4. **Proyección de mantenimientos:**
   - Vista de próximos 30 días
   - Evita atrasos y problemas operativos

## 🎨 Tecnologías Utilizadas

### Frontend
- **Bootstrap 5:** Framework CSS para diseño responsive
- **Chart.js:** Librería para gráficas interactivas
- **Icons:** Material Design Icons (MDI)

### Backend
- **PHP 7.4+:** Procesamiento de datos
- **PDO:** Consultas seguras a la base de datos
- **JSON:** Formato de datos para gráficas

### Base de Datos
- **MySQL 8.0:** Almacenamiento de datos
- **Consultas optimizadas:** JOINs, GROUP BY, agregaciones

## 📱 Diseño Responsive

El dashboard es completamente responsive:
- **Desktop (>1200px):** Vista completa con todas las columnas
- **Tablet (768-1199px):** Tarjetas y tablas adaptadas
- **Mobile (<768px):** Vista apilada, scroll horizontal en tablas

## ⚡ Optimizaciones

### Rendimiento
- Consultas SQL optimizadas con índices
- Límites en resultados (top 10)
- Carga asíncrona de datos para gráficas

### Caché
- Variables de sesión para datos de usuario
- Preparación de statements para reutilización

## 🔒 Seguridad

- Validación de sesión en cada carga
- Filtrado por empresa del usuario
- Escape de datos en HTML (htmlspecialchars)
- Consultas preparadas (PDO bindParam)

## 📊 Ejemplos de Uso

### Caso 1: Planificación de Presupuesto
1. Revisar gráfica de costos mensuales
2. Identificar tendencias de incremento
3. Exportar reporte CSV para análisis financiero

### Caso 2: Optimización de Inventario
1. Consultar tabla de materiales más comprados
2. Identificar top 5 materiales
3. Planificar compra al por mayor

### Caso 3: Prevención de Fallas
1. Revisar próximos mantenimientos
2. Identificar vehículos con menos de 7 días
3. Programar citas con talleres

### Caso 4: Análisis de Flota
1. Ver vehículos con más mantenimientos
2. Comparar costo promedio vs costo total
3. Tomar decisiones de renovación

## 🚀 Futuras Mejoras

### Posibles Extensiones:
1. **Filtros dinámicos:**
   - Por fecha personalizada
   - Por categoría de vehículo
   - Por tipo de mantenimiento

2. **Gráficas adicionales:**
   - Tendencia de costos por categoría
   - Comparativa año vs año
   - Heatmap de mantenimientos

3. **Alertas automáticas:**
   - Email cuando faltan < 7 días
   - Notificaciones push
   - SMS para urgencias

4. **Exportación avanzada:**
   - PDF con gráficas
   - Excel con múltiples hojas
   - API REST para integraciones

5. **Predicciones:**
   - Machine Learning para predecir fallas
   - Análisis predictivo de costos
   - Recomendaciones automatizadas

## 📞 Soporte

Para consultas sobre el dashboard:
1. Verificar que todos los mantenimientos tengan datos correctos
2. Asegurar que las fechas sean coherentes
3. Revisar que los costos estén actualizados
4. Confirmar que los estados estén configurados

## ✅ Checklist de Verificación

Antes de usar el dashboard, asegurar:
- [x] Base de datos creada con structure.sql
- [x] Roles y estados de mantenimiento insertados
- [x] Al menos un usuario administrador creado
- [x] Empresa configurada
- [x] Categorías de vehículos creadas
- [x] Vehículos registrados
- [x] Mantenimientos con datos completos
- [x] Materiales asociados a mantenimientos

## 🎯 KPIs Principales

El dashboard permite monitorear:

1. **Costo Total de Mantenimiento:** Gasto acumulado
2. **Costo Promedio por Vehículo:** Benchmark de eficiencia
3. **Frecuencia de Mantenimiento:** Cantidad por mes
4. **Cumplimiento de Programación:** % de mantenimientos a tiempo
5. **Top 3 Materiales:** Consumo de recursos
6. **Vehículos Críticos:** Mayor necesidad de mantenimiento

---

**Versión:** 1.0  
**Última actualización:** 29 de octubre de 2025  
**Desarrollado por:** Sysmaint Team
