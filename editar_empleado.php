<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Verificar si se recibió el ID del empleado
if (!isset($_GET['id'])) {
    echo "ID de empleado no especificado.";
    exit;
}

$id_empleado = $_GET['id'];

// Conexión a la base de datos
//aki yo caxo k tienen q cambiarlo segun la base de datos k tengan en su pc
$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
if (!$conn) {
    $e = oci_error();
    echo "Error de conexión: " . $e['message'];
    exit;
}

// Consultar datos del empleado
$query = "
    SELECT 
        e.NOMBRE_EMPLEADO,
        e.APELLIDO1_EMPLEADO,
        e.APELLIDO2_EMPLEADO,
        e.NUMERO_TELEFONO,
        b.NRO_CUENTA,
        t.ID_HORA_INGRESO,
        t.ID_HORA_SALIDA
    FROM 
        BC_BR_BV_RM_EMPLEADOS e
    LEFT JOIN 
        BC_BR_BV_RM_DATOS_PAGOS b ON e.ID_EMPLEADO = b.ID_EMPLEADO
    LEFT JOIN 
        BC_BR_BV_RM_TURNOS t ON e.ID_TURNO = t.ID_TURNO
    WHERE 
        e.ID_EMPLEADO = :id_empleado";

$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ':id_empleado', $id_empleado);
oci_execute($stid);

$empleado = oci_fetch_assoc($stid);
oci_free_statement($stid);

if (!$empleado) {
    echo "Empleado no encontrado.";
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefono = $_POST['telefono'];
    $nro_cuenta = $_POST['nro_cuenta'];
    $hora_ingreso = $_POST['hora_ingreso'];
    $hora_salida = $_POST['hora_salida'];

    // Actualizar datos del empleado
    $update_empleado = "
        UPDATE BC_BR_BV_RM_EMPLEADOS 
        SET NUMERO_TELEFONO = :telefono 
        WHERE ID_EMPLEADO = :id_empleado";
    $stid = oci_parse($conn, $update_empleado);
    oci_bind_by_name($stid, ':telefono', $telefono);
    oci_bind_by_name($stid, ':id_empleado', $id_empleado);
    oci_execute($stid);
    oci_free_statement($stid);

    // Actualizar datos bancarios
    $update_bancarios = "
        UPDATE BC_BR_BV_RM_DATOS_PAGOS 
        SET NRO_CUENTA = :nro_cuenta 
        WHERE ID_EMPLEADO = :id_empleado";
    $stid = oci_parse($conn, $update_bancarios);
    oci_bind_by_name($stid, ':nro_cuenta', $nro_cuenta);
    oci_bind_by_name($stid, ':id_empleado', $id_empleado);
    oci_execute($stid);
    oci_free_statement($stid);

    // Actualizar turnos
    $update_turnos = "
        UPDATE BC_BR_BV_RM_TURNOS 
        SET HORA_INGRESO = :hora_ingreso, HORA_SALIDA = :hora_salida
        WHERE ID_TURNO = (SELECT ID_TURNO FROM BC_BR_BV_RM_EMPLEADOS WHERE ID_EMPLEADO = :id_empleado)";
    $stid = oci_parse($conn, $update_turnos);
    oci_bind_by_name($stid, ':hora_ingreso', $hora_ingreso);
    oci_bind_by_name($stid, ':hora_salida', $hora_salida);
    oci_bind_by_name($stid, ':id_empleado', $id_empleado);
    oci_execute($stid);
    oci_free_statement($stid);

    echo "Datos actualizados correctamente.";
    header("Location: empleados.php");
    exit;
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Editar Empleado</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="telefono">Número de Teléfono:</label>
                <input type="text" id="telefono" name="telefono" value="<?php echo htmlentities($empleado['NUMERO_TELEFONO']); ?>" required>
            </div>
            <div class="form-group">
                <label for="nro_cuenta">Número de Cuenta:</label>
                <input type="text" id="nro_cuenta" name="nro_cuenta" value="<?php echo htmlentities($empleado['NRO_CUENTA']); ?>" required>
            </div>
            <div class="form-group">
                <label for="hora_ingreso">Hora de Ingreso:</label>
                <input type="text" id="hora_ingreso" name="hora_ingreso" value="<?php echo htmlentities($empleado['HORA_INGRESO']); ?>" required>
            </div>
            <div class="form-group">
                <label for="hora_salida">Hora de Salida:</label>
                <input type="text" id="hora_salida" name="hora_salida" value="<?php echo htmlentities($empleado['HORA_SALIDA']); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit">Guardar Cambios</button>
            </div>
        </form>
    </div>
</body>
</html>
