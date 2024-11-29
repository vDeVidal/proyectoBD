<?php
session_start();

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Procesar el inicio de sesión
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;

    // Validar que ambos campos no estén vacíos
    if ($username && $password) {
        // Consultar al usuario en la base de datos
        $query = "SELECT ID_EMPLEADO, USERNAME, PASSWORD, ID_SESION, PRIMERA_VEZ_INICIO_SESION 
                  FROM BC_BR_BV_RM_EMPLEADOS 
                  WHERE USERNAME = :username";
        $stid = oci_parse($conn, $query);
        oci_bind_by_name($stid, ':username', $username);
        oci_execute($stid);

        $user = oci_fetch_assoc($stid);

        if ($user && $password === $user['PASSWORD']) {
            $_SESSION['id_empleado'] = $user['ID_EMPLEADO'];
            $_SESSION['id_sesion'] = $user['ID_SESION'];
            $_SESSION['username'] = $user['USERNAME'];

            // Verificar si es la primera vez que inicia sesión
            if ($user['PRIMERA_VEZ_INICIO_SESION'] == 1) {
                header('Location: cambiar_contrasena.php');
                exit;
            }

            // Redirigir según el rol
            if ($user['ID_SESION'] == 1) { // Admin
                // Para depuración: Muestra que es un administrador y redirige
                // echo "Redirigiendo a empleados.php"; 
                header('Location: empleados.php');
                exit;
            } else { // Usuario normal
                header('Location: usuario.php');
                exit;
            }
        } else {
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $error = "Por favor, complete todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <img src="img/fondo2.jpeg" alt="Fondo" class="background-image">
    <div class="login-container">
        <h1>Iniciar Sesión</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Ingrese su usuario" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" placeholder="Ingrese su contraseña" required>
            </div>
            <div class="form-group">
                <button type="submit">Iniciar Sesión</button>
            </div>
        </form>
    </div>
</body>
</html>
