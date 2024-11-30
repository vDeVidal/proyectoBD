<?php
// Iniciar sesión
session_start();
include('navbar.php');

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Obtener el ID del empleado autenticado
$id_empleado = $_SESSION['id_empleado'];

// Consulta para obtener los datos del empleado
$query_empleado = "
    SELECT 
        e.NOMBRE_EMPLEADO, 
        e.APELLIDO1_EMPLEADO, 
        e.APELLIDO2_EMPLEADO,
        e.NUMERO_TELEFONO,
        e.HORAS_EXTRAS,
        e.SUELDO AS SUELDO_BASE,
        a.NOMBRE_AREA_TRABAJO,
        a.VALOR_HORA_EXTRA, -- Nuevo campo
        s.NOMBRE_SUCURSAL,
        TO_CHAR(e.FECHA_CONTRATACION, 'DD/MM/YYYY') AS FECHA_CONTRATACION
    FROM BC_BR_BV_RM_EMPLEADOS e
    JOIN BC_BR_BV_RM_AREA_TRABAJO a ON e.ID_AREA_TRABAJO = a.ID_AREA_TRABAJO
    JOIN BC_BR_BV_RM_SUCURSAL s ON e.ID_SUCURSAL = s.ID_SUCURSAL
    WHERE e.ID_EMPLEADO = :id_empleado";

$stid_empleado = oci_parse($conn, $query_empleado);
oci_bind_by_name($stid_empleado, ':id_empleado', $id_empleado);
oci_execute($stid_empleado);

$empleado = oci_fetch_assoc($stid_empleado);

if (!$empleado) {
    die("Empleado no encontrado.");
}

// Calcular el sueldo total
$sueldo_base = $empleado['SUELDO_BASE'];
$valor_hora_extra = $empleado['VALOR_HORA_EXTRA'];
$horas_extras = $empleado['HORAS_EXTRAS'];
$sueldo_total = $sueldo_base + ($valor_hora_extra * $horas_extras);

// Consulta para obtener los turnos del empleado
$query_turnos = "
    SELECT 
        TO_CHAR(t.FECHA, 'DD/MM/YYYY') AS FECHA,
        TO_CHAR(t.HORA_INGRESO, 'HH24:MI') AS HORA_INGRESO,
        TO_CHAR(t.HORA_SALIDA, 'HH24:MI') AS HORA_SALIDA
    FROM BC_BR_BV_RM_TURNOS t
    WHERE t.ID_EMPLEADO = :id_empleado";

$stid_turnos = oci_parse($conn, $query_turnos);
oci_bind_by_name($stid_turnos, ':id_empleado', $id_empleado);
oci_execute($stid_turnos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/usuario.css">
</head>
<body>
    <img src="img/fondo2.jpeg" alt="Fondo" class="background">
    <div class="container">
        <h1>Bienvenid@, <?php echo htmlentities($empleado['NOMBRE_EMPLEADO']); ?></h1>
        <h2>Información Personal</h2>
        <p><strong>Nombre:</strong> <?php echo htmlentities($empleado['NOMBRE_EMPLEADO']); ?></p>
        <p><strong>Apellidos:</strong> <?php echo htmlentities($empleado['APELLIDO1_EMPLEADO']) . " " . htmlentities($empleado['APELLIDO2_EMPLEADO']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlentities($empleado['NUMERO_TELEFONO']); ?></p>
        <p><strong>Sueldo Base:</strong> $<?php echo number_format($sueldo_base, 0, ',', '.'); ?></p>
        <p><strong>Horas Extras:</strong> <?php echo htmlentities($empleado['HORAS_EXTRAS']); ?></p>
        <p><strong>Valor Hora Extra:</strong> $<?php echo number_format($valor_hora_extra, 0, ',', '.'); ?></p>
        <p><strong>Sueldo Total:</strong> $<?php echo number_format($sueldo_total, 0, ',', '.'); ?></p>
        <p><strong>Fecha de Contratación:</strong> <?php echo htmlentities($empleado['FECHA_CONTRATACION']); ?></p>
        <p><strong>Área de Trabajo:</strong> <?php echo htmlentities($empleado['NOMBRE_AREA_TRABAJO']); ?></p>
        <p><strong>Sucursal:</strong> <?php echo htmlentities($empleado['NOMBRE_SUCURSAL']); ?></p>

        <h2>Turnos</h2>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora de Ingreso</th>
                    <th>Hora de Salida</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = oci_fetch_assoc($stid_turnos)) {
                    echo "<tr>
                            <td>" . htmlentities($row['FECHA']) . "</td>
                            <td>" . htmlentities($row['HORA_INGRESO']) . "</td>
                            <td>" . htmlentities($row['HORA_SALIDA']) . "</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
