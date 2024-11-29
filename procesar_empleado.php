<?php
// Iniciar sesión para identificar al usuario
session_start();

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Validar si el método de la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar y recoger los datos enviados desde el formulario
    $nombre = $_POST['nombre'] ?? null;
    $apellido1 = $_POST['apellido1'] ?? null;
    $apellido2 = $_POST['apellido2'] ?? null;
    $fecha_contratacion = date('d/m/Y'); // Usamos la fecha actual
    $telefono = $_POST['telefono'] ?? null;
    $id_area_trabajo = $_POST['id_area_trabajo'] ?? null;
    $id_sucursal = $_POST['id_sucursal'] ?? null;
    $horas_extras = $_POST['horas_extras'] ?? 0; // Si no se envía, usar 0
    $id_sesion = $_POST['id_sesion'] ?? 0; // Por defecto, no admin
    $nro_cuenta = $_POST['nro_cuenta'] ?? null;
    $id_tipo_cuenta = $_POST['id_tipo_cuenta'] ?? null;
    $id_banco_empleado = $_POST['id_banco_empleado'] ?? null;
    $sueldo = $_POST['sueldo'] ?? null;


    // Validar que no haya campos vacíos
    if (!$nombre || !$apellido1 || !$apellido2 || !$telefono || !$id_area_trabajo || !$id_sucursal || !$nro_cuenta || !$id_tipo_cuenta || !$id_banco_empleado || !$sueldo) {
        die("Error: Todos los campos son obligatorios.");
    }

    // Preparar y ejecutar la llamada al procedimiento almacenado
    $sql = 'BEGIN BC_BR_BV_RM_INSERTAR_EMPLEADO(:nombre, :apellido1, :apellido2, TO_DATE(:fecha_contratacion, \'DD/MM/YYYY\'), :telefono, :id_area_trabajo, :id_sucursal, :horas_extras, :id_sesion, :nro_cuenta, :id_tipo_cuenta, :id_banco_empleado, :sueldo); END;';
    $stid = oci_parse($conn, $sql);

    // Asociar los parámetros con las variables PHP
    oci_bind_by_name($stid, ':nombre', $nombre);
    oci_bind_by_name($stid, ':apellido1', $apellido1);
    oci_bind_by_name($stid, ':apellido2', $apellido2);
    oci_bind_by_name($stid, ':fecha_contratacion', $fecha_contratacion);
    oci_bind_by_name($stid, ':telefono', $telefono);
    oci_bind_by_name($stid, ':id_area_trabajo', $id_area_trabajo);
    oci_bind_by_name($stid, ':id_sucursal', $id_sucursal);
    oci_bind_by_name($stid, ':horas_extras', $horas_extras);
    oci_bind_by_name($stid, ':id_sesion', $id_sesion);
    oci_bind_by_name($stid, ':nro_cuenta', $nro_cuenta);
    oci_bind_by_name($stid, ':id_tipo_cuenta', $id_tipo_cuenta);
    oci_bind_by_name($stid, ':id_banco_empleado', $id_banco_empleado);
    oci_bind_by_name($stid, ':sueldo', $sueldo);

    // Ejecutar la consulta y verificar si hay errores
    if (oci_execute($stid)) {
        echo "Empleado agregado exitosamente.";
        header("Location: empleados.php"); // Redirigir a la lista de empleados
        exit;
    } else {
        $e = oci_error($stid);
        die("Error al insertar empleado: " . $e['message']);
    }
}

// Si no se envió una solicitud POST, redirigir a empleados.php
header("Location: empleados.php");
exit;
?>
