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

// Consultar los nombres de los tipos de cuenta
$query_tipo_cuenta = "SELECT ID_TIPO_CUENTA, TIPO_CUENTA FROM BC_BR_BV_RM_TIPO_CUENTA";
$stid_tipo_cuenta = oci_parse($conn, $query_tipo_cuenta);
if (!oci_execute($stid_tipo_cuenta)) {
    $e = oci_error($stid_tipo_cuenta);
    die("Error al ejecutar la consulta de tipo de cuenta: " . $e['message']);
}

// Consultar los nombres de los bancos
$query_banco = "SELECT ID_BANCO_EMPLEADO, NOMBRE_BANCO FROM BC_BR_BV_RM_BANCO_EMPLEADO";
$stid_banco = oci_parse($conn, $query_banco);
if (!oci_execute($stid_banco)) {
    $e = oci_error($stid_banco);
    die("Error al ejecutar la consulta de bancos: " . $e['message']);
}

// Consultar los nombres de las sucursales
$query_sucursal = "SELECT ID_SUCURSAL, NOMBRE_SUCURSAL FROM BC_BR_BV_RM_SUCURSAL";
$stid_sucursal = oci_parse($conn, $query_sucursal);
if (!oci_execute($stid_sucursal)) {
    $e = oci_error($stid_sucursal);
}

// Consultar los nombres de las areas de trabajo
$query_area_trabajo = "SELECT ID_AREA_TRABAJO, NOMBRE_AREA_TRABAJO FROM BC_BR_BV_RM_AREA_TRABAJO";
$stid_area_trabajo = oci_parse($conn, $query_area_trabajo);
if (!oci_execute($stid_area_trabajo)) {
    $e = oci_error($stid_area_trabajo);
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Empleado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
        }
        .form-container h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
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
        <h2>Agregar Empleado</h2>
        <form method="POST" action="procesar_empleado.php">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="apellido1">Apellido Paterno:</label>
                <input type="text" id="apellido1" name="apellido1" required>
            </div>
            <div class="form-group">
                <label for="apellido2">Apellido Materno:</label>
                <input type="text" id="apellido2" name="apellido2" required>
            </div>
            <div class="form-group">
                <label for="telefono">Número de Teléfono:</label>
                <div class="phone-input-wrapper">
                    <span class="phone-prefix">+569</span>
                    <input type="text" id="telefono" name="telefono" class="phone-input" placeholder="Ingrese el número" maxlength="8" pattern="[0-9]{8}" required>
                </div>
            </div>
            <div class="form-group">
                <label for="id_sucursal">ID Sucursal:</label>
                <select id="id_sucursal" name="id_sucursal" required>
                    <option value="">Seleccione una Sucursal</option>
                    <?php
                    oci_execute($stid_sucursal); // Ejecutar nuevamente para reiniciar el cursor
                    while ($row = oci_fetch_assoc($stid_sucursal)) {
                        echo "<option value='" . $row['ID_SUCURSAL'] . "'>" . htmlentities($row['NOMBRE_SUCURSAL']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_area_trabajo">ID Área de Trabajo:</label>
                <select id="id_area_trabajo" name="id_area_trabajo" required>
                    <option value="">Seleccione un Area</option>
                    <?php
                    oci_execute($stid_area_trabajo); // Ejecutar nuevamente para reiniciar el cursor
                    while ($row = oci_fetch_assoc($stid_area_trabajo)) {
                        echo "<option value='" . $row['ID_AREA_TRABAJO'] . "'>" . htmlentities($row['NOMBRE_AREA_TRABAJO']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            
            <h3>Datos Bancarios</h3>
            <div class="form-group">
                <label for="nro_cuenta">Número de Cuenta:</label>
                <input type="text" id="nro_cuenta" name="nro_cuenta" required>
            </div>
            <div class="form-group">
                <label for="id_tipo_cuenta">Tipo de Cuenta:</label>
                <select id="id_tipo_cuenta" name="id_tipo_cuenta" required>
                    <option value="">Seleccione un Tipo</option>
                    <?php
                    oci_execute($stid_tipo_cuenta); // Ejecutar nuevamente para reiniciar el cursor
                    while ($row = oci_fetch_assoc($stid_tipo_cuenta)) {
                        echo "<option value='" . $row['ID_TIPO_CUENTA'] . "'>" . htmlentities($row['TIPO_CUENTA']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="id_banco_empleado">Banco:</label>
                <select id="id_banco_empleado" name="id_banco_empleado" required>
                    <option value="">Seleccione un Banco</option>
                    <?php
                    oci_execute($stid_banco); // Ejecutar nuevamente para reiniciar el cursor
                    while ($row = oci_fetch_assoc($stid_banco)) {
                        echo "<option value='" . $row['ID_BANCO_EMPLEADO'] . "'>" . htmlentities($row['NOMBRE_BANCO']) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Agregar Empleado</button>
            </div>
        </form>
    </div>
</body>
</html>
