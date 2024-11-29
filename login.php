<?php
session_start();

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
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
                header('Location: empleados.php');
            } else { // Usuario normal
                header('Location: usuario.php');
            }
            exit;
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 300px;
            text-align: center;
        }
        .login-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333333;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
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
            background-color: #007BFF;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
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