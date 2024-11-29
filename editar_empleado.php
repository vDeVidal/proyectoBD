<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Obtener el ID del empleado desde la URL
$id_empleado = $_GET['id'] ?? null;

if (!$id_empleado) {
    die("ID de empleado no proporcionado.");
}

// Consultar los datos del empleado
$query_empleado = "SELECT * FROM BC_BR_BV_RM_EMPLEADOS WHERE ID_EMPLEADO = :id_empleado";
$stid_empleado = oci_parse($conn, $query_empleado);
oci_bind_by_name($stid_empleado, ":id_empleado", $id_empleado);

if (!oci_execute($stid_empleado)) {
    $e = oci_error($stid_empleado);
    die("Error al ejecutar la consulta del empleado: " . $e['message']);
}

$empleado = oci_fetch_assoc($stid_empleado);

if (!$empleado) {
    die("Empleado no encontrado.");
}

// Consultar los datos bancarios del empleado
$query_datos_bancarios = "SELECT * FROM BC_BR_BV_RM_DATOS_PAGOS WHERE ID_EMPLEADO = :id_empleado";
$stid_bancarios = oci_parse($conn, $query_datos_bancarios);
oci_bind_by_name($stid_bancarios, ":id_empleado", $id_empleado);
oci_execute($stid_bancarios);

$datos_bancarios = oci_fetch_assoc($stid_bancarios);

// Consultar sucursales
$query_sucursales = "SELECT ID_SUCURSAL, NOMBRE_SUCURSAL FROM BC_BR_BV_RM_SUCURSAL";
$stid_sucursales = oci_parse($conn, $query_sucursales);
oci_execute($stid_sucursales);

// Consultar áreas de trabajo
$query_areas = "SELECT ID_AREA_TRABAJO, NOMBRE_AREA_TRABAJO FROM BC_BR_BV_RM_AREA_TRABAJO";
$stid_areas = oci_parse($conn, $query_areas);
oci_execute($stid_areas);

// Consultar tipos de cuenta
$query_tipos_cuenta = "SELECT ID_TIPO_CUENTA, TIPO_CUENTA FROM BC_BR_BV_RM_TIPO_CUENTA";
$stid_tipos_cuenta = oci_parse($conn, $query_tipos_cuenta);
oci_execute($stid_tipos_cuenta);

// Consultar bancos
$query_bancos = "SELECT ID_BANCO_EMPLEADO, NOMBRE_BANCO FROM BC_BR_BV_RM_BANCO_EMPLEADO";
$stid_bancos = oci_parse($conn, $query_bancos);
oci_execute($stid_bancos);

// Procesar la actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? null;
    $apellido1 = $_POST['apellido1'] ?? null;
    $apellido2 = $_POST['apellido2'] ?? null;
    $telefono = $_POST['telefono'] ?? null;
    $id_area_trabajo = $_POST['id_area_trabajo'] ?? null;
    $id_sucursal = $_POST['id_sucursal'] ?? null;
    $sueldo = $_POST['sueldo'] ?? null;

    // Actualizar datos del empleado
    $update_empleado = "UPDATE BC_BR_BV_RM_EMPLEADOS 
                        SET NOMBRE_EMPLEADO = :nombre, 
                            APELLIDO1_EMPLEADO = :apellido1, 
                            APELLIDO2_EMPLEADO = :apellido2, 
                            NUMERO_TELEFONO = :telefono, 
                            ID_AREA_TRABAJO = :id_area_trabajo, 
                            ID_SUCURSAL = :id_sucursal,
                            SUELDO = :sueldo 
                        WHERE ID_EMPLEADO = :id_empleado";

    $stid_update_empleado = oci_parse($conn, $update_empleado);
    oci_bind_by_name($stid_update_empleado, ":nombre", $nombre);
    oci_bind_by_name($stid_update_empleado, ":apellido1", $apellido1);
    oci_bind_by_name($stid_update_empleado, ":apellido2", $apellido2);
    oci_bind_by_name($stid_update_empleado, ":telefono", $telefono);
    oci_bind_by_name($stid_update_empleado, ":id_area_trabajo", $id_area_trabajo);
    oci_bind_by_name($stid_update_empleado, ":id_sucursal", $id_sucursal);
    oci_bind_by_name($stid_update_empleado, ":id_empleado", $id_empleado);
    oci_bind_by_name($stid_update_empleado, ":sueldo", $sueldo);
    oci_execute($stid_update_empleado);

    // Actualizar datos bancarios
    $nro_cuenta = $_POST['nro_cuenta'] ?? null;
    $id_tipo_cuenta = $_POST['id_tipo_cuenta'] ?? null;
    $id_banco_empleado = $_POST['id_banco_empleado'] ?? null;

    if ($datos_bancarios) {
        $update_bancarios = "UPDATE BC_BR_BV_RM_DATOS_PAGOS 
                             SET NRO_CUENTA = :nro_cuenta, 
                                 ID_TIPO_CUENTA = :id_tipo_cuenta, 
                                 ID_BANCO_EMPLEADO = :id_banco_empleado 
                             WHERE ID_EMPLEADO = :id_empleado";

        $stid_update_bancarios = oci_parse($conn, $update_bancarios);
        oci_bind_by_name($stid_update_bancarios, ":nro_cuenta", $nro_cuenta);
        oci_bind_by_name($stid_update_bancarios, ":id_tipo_cuenta", $id_tipo_cuenta);
        oci_bind_by_name($stid_update_bancarios, ":id_banco_empleado", $id_banco_empleado);
        oci_bind_by_name($stid_update_bancarios, ":id_empleado", $id_empleado);
        oci_execute($stid_update_bancarios);
    } else {
        $insert_bancarios = "INSERT INTO BC_BR_BV_RM_DATOS_PAGOS (ID_EMPLEADO, NRO_CUENTA, ID_TIPO_CUENTA, ID_BANCO_EMPLEADO) 
                             VALUES (:id_empleado, :nro_cuenta, :id_tipo_cuenta, :id_banco_empleado)";

        $stid_insert_bancarios = oci_parse($conn, $insert_bancarios);
        oci_bind_by_name($stid_insert_bancarios, ":nro_cuenta", $nro_cuenta);
        oci_bind_by_name($stid_insert_bancarios, ":id_tipo_cuenta", $id_tipo_cuenta);
        oci_bind_by_name($stid_insert_bancarios, ":id_banco_empleado", $id_banco_empleado);
        oci_bind_by_name($stid_insert_bancarios, ":id_empleado", $id_empleado);
        oci_execute($stid_insert_bancarios);
    }

    header("Location: empleados.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/estilos.css">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Editar Empleado</h2>
        <form method="POST" action="">
            <!-- Datos del Empleado -->
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?php echo htmlentities($empleado['NOMBRE_EMPLEADO']); ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido1">Apellido Paterno:</label>
                <input type="text" id="apellido1" name="apellido1" class="form-control" value="<?php echo htmlentities($empleado['APELLIDO1_EMPLEADO']); ?>" required>
            </div>
            <div class="form-group">
                <label for="apellido2">Apellido Materno:</label>
                <input type="text" id="apellido2" name="apellido2" class="form-control" value="<?php echo htmlentities($empleado['APELLIDO2_EMPLEADO']); ?>" required>
            </div>
            <div class="form-group">
                <label for="telefono">Número de Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="form-control" value="<?php echo htmlentities($empleado['NUMERO_TELEFONO']); ?>" required>
            </div>
            <div class="form-group">
                <label for="sueldo">Sueldo:</label>
                <input type="number" id="sueldo" name="sueldo" class="form-control" value="<?php echo htmlentities($empleado['SUELDO']); ?>" required>
            </div>
                <div class="form-group">
                <label for="id_sucursal">Sucursal:</label>
                <select id="id_sucursal" name="id_sucursal" class="form-control" required>
                    <?php while ($sucursal = oci_fetch_assoc($stid_sucursales)): ?>
                        <option value="<?php echo $sucursal['ID_SUCURSAL']; ?>" <?php echo ($empleado['ID_SUCURSAL'] ?? null) == $sucursal['ID_SUCURSAL'] ? 'selected' : ''; ?>>
                            <?php echo htmlentities($sucursal['NOMBRE_SUCURSAL']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_area_trabajo">Área de Trabajo:</label>
                <select id="id_area_trabajo" name="id_area_trabajo" class="form-control" required>
                    <?php while ($area = oci_fetch_assoc($stid_areas)): ?>
                        <option value="<?php echo $area['ID_AREA_TRABAJO']; ?>" <?php echo ($empleado['ID_AREA_TRABAJO'] ?? null) == $area['ID_AREA_TRABAJO'] ? 'selected' : ''; ?>>
                            <?php echo htmlentities($area['NOMBRE_AREA_TRABAJO']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Datos Bancarios -->
            <h3>Datos Bancarios</h3>
            <div class="form-group">
                <label for="nro_cuenta">Número de Cuenta:</label>
                <input type="text" id="nro_cuenta" name="nro_cuenta" class="form-control" value="<?php echo htmlentities($datos_bancarios['NRO_CUENTA'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="id_tipo_cuenta">Tipo de Cuenta:</label>
                <select id="id_tipo_cuenta" name="id_tipo_cuenta" class="form-control">
                    <option value="">Seleccione un Tipo</option>
                    <?php while ($tipo = oci_fetch_assoc($stid_tipos_cuenta)): ?>
                        <option value="<?php echo $tipo['ID_TIPO_CUENTA']; ?>" <?php echo ($datos_bancarios['ID_TIPO_CUENTA'] ?? null) == $tipo['ID_TIPO_CUENTA'] ? 'selected' : ''; ?>>
                            <?php echo htmlentities($tipo['TIPO_CUENTA']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_banco_empleado">Banco:</label>
                <select id="id_banco_empleado" name="id_banco_empleado" class="form-control">
                    <option value="">Seleccione un Banco</option>
                    <?php while ($banco = oci_fetch_assoc($stid_bancos)): ?>
                        <option value="<?php echo $banco['ID_BANCO_EMPLEADO']; ?>" <?php echo ($datos_bancarios['ID_BANCO_EMPLEADO'] ?? null) == $banco['ID_BANCO_EMPLEADO'] ? 'selected' : ''; ?>>
                            <?php echo htmlentities($banco['NOMBRE_BANCO']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-block">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
