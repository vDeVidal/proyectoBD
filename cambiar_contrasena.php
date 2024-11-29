<?php
session_start();

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Procesar el cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_empleado = $_SESSION['id_empleado'];
    $nueva_contrasena = $_POST['nueva_contrasena'];

    // Actualizar la contraseña y marcar que ya no es la primera vez
    $query = "UPDATE BC_BR_BV_RM_EMPLEADOS 
              SET PASSWORD = :password, PRIMERA_VEZ_INICIO_SESION = 0 
              WHERE ID_EMPLEADO = :id_empleado";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":password", $nueva_contrasena);
    oci_bind_by_name($stid, ":id_empleado", $id_empleado);

    if (oci_execute($stid)) {
        // Obtener el tipo de usuario para redirigir
        $query_tipo_usuario = "SELECT ID_SESION FROM BC_BR_BV_RM_EMPLEADOS WHERE ID_EMPLEADO = :id_empleado";
        $stid_tipo = oci_parse($conn, $query_tipo_usuario);
        oci_bind_by_name($stid_tipo, ":id_empleado", $id_empleado);
        oci_execute($stid_tipo);

        $user = oci_fetch_assoc($stid_tipo);
        
        if ($user) {
            // Redirigir según el tipo de usuario
            if ($user['ID_SESION'] == 1) { // Admin
                header('Location: empleados.php');
            } else { // Usuario normal
                header('Location: usuario.php');
            }
            exit;
        } else {
            die("Error al obtener el tipo de usuario.");
        }
    } else {
        $e = oci_error($stid);
        die("Error al actualizar la contraseña: " . $e['message']);
    }
}

oci_close($conn);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/contrasena.css">
</head>
<body>
    <!-- Imagen cargada desde HTML -->
    <img src="img/fondo2.jpeg" alt="Fondo" class="background">

    <!-- Contenedor del contenido -->
    <div class="container">
        <h2>Cambiar Contraseña</h2>
        <form method="POST">
            <label for="nueva_contrasena">Nueva Contraseña:</label>
            <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
            <button type="submit">Actualizar Contraseña</button>
        </form>
    </div>
</body>
</html>



