<?php
session_start();
//if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
 //   header('Location: login.php');
 //   exit;
//} 

$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_SESSION['id_empleado'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    $query = "UPDATE BC_BR_BV_RM_EMPLEADOS SET PASSWORD = :password, PRIMERA_VEZ_INICIO_SESION = 0 WHERE ID_EMPLEADO = :id_empleado";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":password", $nueva_contrasena);
    oci_bind_by_name($stid, ":id_empleado", $id_empleado);

    if (oci_execute($stid)) {
        echo "Contraseña actualizada exitosamente. Redirigiendo...";
        header('refresh:2;url=usuario.php');
    } else {
        $e = oci_error($stid);
        echo "Error al actualizar la contraseña: " . $e['message'];
    }
}
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Contraseña</title>
</head>
<body>
    <h2>Cambiar Contraseña</h2>
    <form method="POST">
        <label for="nueva_contrasena">Nueva Contraseña:</label>
        <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
        <button type="submit">Actualizar Contraseña</button>
    </form>
</body>
</html>
