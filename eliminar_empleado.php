<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Conexión a la base de datos
//aki yo caxo k tienen q cambiarlo segun la base de datos k tengan en su pc
$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Verificar si se ha proporcionado el ID del empleado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID del empleado no proporcionado.");
}

$id_empleado = $_GET['id'];

try {
    // Eliminar al empleado
    $query = "DELETE FROM BC_BR_BV_RM_EMPLEADOS WHERE ID_EMPLEADO = :id_empleado";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ':id_empleado', $id_empleado);

    if (!oci_execute($stid, OCI_COMMIT_ON_SUCCESS)) {
        $e = oci_error($stid);
        throw new Exception("Error al eliminar el empleado: " . $e['message']);
    }

    // Redirigir con mensaje de éxito
    header("Location: empleados.php?mensaje=Empleado eliminado exitosamente");
    exit;

} catch (Exception $e) {
    die("Ocurrió un error: " . $e->getMessage());
}

// Cerrar recursos
oci_free_statement($stid);
oci_close($conn);
?>
