<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_POST['id_empleado'];
    $fecha = $_POST['fecha']; // Fecha en formato YYYY-MM-DD desde el formulario
    $hora_ingreso = $_POST['hora_ingreso']; // Hora de ingreso (HH:MI)
    $hora_salida = $_POST['hora_salida'];   // Hora de salida (HH:MI)

    // Conexión a la base de datos
    $conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
    if (!$conn) {
        $e = oci_error();
        die("Error de conexión: " . $e['message']);
    }

    // Convertir la fecha de "YYYY-MM-DD" a "DD/MM/YYYY"
    $fecha_formateada = date('d/m/Y', strtotime($fecha));

    // Convertir horas (sin cambiar el formato de fecha predeterminado)
    $hora_ingreso_formateada = "TO_DATE('$hora_ingreso', 'HH24:MI')";
    $hora_salida_formateada = "TO_DATE('$hora_salida', 'HH24:MI')";

    // Consulta SQL para insertar los datos
    $query = "INSERT INTO BC_BR_BV_RM_TURNOS (ID_TURNO, ID_EMPLEADO, FECHA, HORA_INGRESO, HORA_SALIDA) 
              VALUES (
                  BC_BR_BV_RM_ID_TURNO.NEXTVAL,
                  :id_empleado,
                  TO_DATE(:fecha, 'DD/MM/YYYY'),
                  $hora_ingreso_formateada,
                  $hora_salida_formateada
              )";

    // Preparar y ejecutar la consulta
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ':id_empleado', $id_empleado);
    oci_bind_by_name($stid, ':fecha', $fecha_formateada);

    if (oci_execute($stid)) {
        header('Location: empleados.php');
        exit;
    } else {
        $e = oci_error($stid);
        die("Error al guardar el turno: " . $e['message']);
    }
}
?>
