<?php
// Conexión a la base de datos
//aki yo caxo k tienen q cambiarlo segun la base de datos k tengan en su pc
$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Datos del formulario
$nombre = $_POST['nombre'];
$apellido1 = $_POST['apellido1'];
$apellido2 = $_POST['apellido2'];
$telefono = $_POST['telefono'];
$id_area_trabajo = $_POST['id_area_trabajo'];
$id_sucursal = $_POST['id_sucursal'];
$fecha_contratacion = date('d-m-Y'); // Fecha actual en formato Oracle

// Preparar la llamada al procedimiento
//se ocupa el procedimiento almacenado
$query = "BEGIN insertar_empleado(
            :nombre, :apellido1, :apellido2,
            TO_DATE(:fecha_contratacion, 'DD-MM-YYYY'),
            :telefono, :id_area_trabajo, :id_sucursal, NULL, 0, NULL
          ); END;";
$stid = oci_parse($conn, $query);

// Bind de parámetros
oci_bind_by_name($stid, ':nombre', $nombre);
oci_bind_by_name($stid, ':apellido1', $apellido1);
oci_bind_by_name($stid, ':apellido2', $apellido2);
oci_bind_by_name($stid, ':fecha_contratacion', $fecha_contratacion);
oci_bind_by_name($stid, ':telefono', $telefono);
oci_bind_by_name($stid, ':id_area_trabajo', $id_area_trabajo);
oci_bind_by_name($stid, ':id_sucursal', $id_sucursal);

// Ejecutar el procedimiento
if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Error al insertar empleado: " . $e['message']);
}
header("Location: empleados.php");
echo "Empleado agregado exitosamente.";
oci_free_statement($stid);
oci_close($conn);
?>

