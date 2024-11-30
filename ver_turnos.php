<?php
session_start();
include('navbar.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Obtener el ID del empleado desde la URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die('Empleado no especificado.');
}

$id_empleado = $_GET['id'];

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Consultar los turnos del empleado
$query = "SELECT FECHA, TO_CHAR(HORA_INGRESO, 'HH24:MI') AS HORA_INGRESO, TO_CHAR(HORA_SALIDA, 'HH24:MI') AS HORA_SALIDA 
          FROM BC_BR_BV_RM_TURNOS 
          WHERE ID_EMPLEADO = :id_empleado 
          ORDER BY FECHA DESC";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ':id_empleado', $id_empleado);

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Error al obtener los turnos: " . $e['message']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Turnos del Empleado</title>
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/turno.css">
</head>
<body>
    <div class="container">
        <h2>Turnos del Empleado</h2>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora de Ingreso</th>
                    <th>Hora de Salida</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = oci_fetch_assoc($stid)): ?>
                    <tr>
                        <td><?php echo htmlentities($row['FECHA']); ?></td>
                        <td><?php echo htmlentities($row['HORA_INGRESO']); ?></td>
                        <td><?php echo htmlentities($row['HORA_SALIDA']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <br>
        <a href="empleados.php">Volver a la Lista de Empleados</a>
    </div>
</body>
</html>
