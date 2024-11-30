<?php
session_start();
include('navbar.php');

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
if (!$conn) {
    die("Error de conexión: " . oci_error());
}

// Obtener ID del empleado
$id_empleado = $_GET['id'];

// Consulta para obtener los datos del empleado
$query = "
    SELECT 
        e.NOMBRE_EMPLEADO, 
        e.APELLIDO1_EMPLEADO, 
        e.APELLIDO2_EMPLEADO,
        e.SUELDO AS SUELDO_BASE,
        e.HORAS_EXTRAS,
        a.VALOR_HORA_EXTRA,
        TO_CHAR(e.FECHA_CONTRATACION, 'DD/MM/YYYY') AS FECHA_CONTRATACION,
        s.NOMBRE_SUCURSAL,
        a.NOMBRE_AREA_TRABAJO
    FROM BC_BR_BV_RM_EMPLEADOS e
    JOIN BC_BR_BV_RM_AREA_TRABAJO a ON e.ID_AREA_TRABAJO = a.ID_AREA_TRABAJO
    JOIN BC_BR_BV_RM_SUCURSAL s ON e.ID_SUCURSAL = s.ID_SUCURSAL
    WHERE e.ID_EMPLEADO = :id_empleado";
    
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ':id_empleado', $id_empleado);
oci_execute($stid);

$empleado = oci_fetch_assoc($stid);

if (!$empleado) {
    die("Empleado no encontrado.");
}

// Calcular sueldo total
$sueldo_base = $empleado['SUELDO_BASE'];
$horas_extras = $empleado['HORAS_EXTRAS'];
$valor_hora_extra = $empleado['VALOR_HORA_EXTRA'];
$sueldo_total = $sueldo_base + ($horas_extras * $valor_hora_extra);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Sueldo</title>
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/empleados.css">
</head>
<body>
<img src="img/fondo2.jpeg" alt="Fondo" class="background">
    <div class="container   ">
        <h1>Informacion</h1>
        <p><strong>Nombre:</strong> <?php echo htmlentities($empleado['NOMBRE_EMPLEADO']); ?></p>
        <p><strong>Apellido Paterno:</strong> <?php echo htmlentities($empleado['APELLIDO1_EMPLEADO']); ?></p>
        <p><strong>Apellido Materno:</strong> <?php echo htmlentities($empleado['APELLIDO2_EMPLEADO']); ?></p>
        <p><strong>Área de Trabajo:</strong> <?php echo htmlentities($empleado['NOMBRE_AREA_TRABAJO']); ?></p>
        <p><strong>Sucursal:</strong> <?php echo htmlentities($empleado['NOMBRE_SUCURSAL']); ?></p>
        <p><strong>Sueldo Base:</strong> $<?php echo number_format($sueldo_base, 0, ',', '.'); ?></p>
        <p><strong>Horas Extras:</strong> <?php echo htmlentities($horas_extras); ?></p>
        <p><strong>Valor Hora Extra:</strong> $<?php echo number_format($valor_hora_extra, 0, ',', '.'); ?></p>
        <p><strong>Sueldo Total:</strong> $<?php echo number_format($sueldo_total, 0, ',', '.'); ?></p>
    </div>
</body>
</html>
