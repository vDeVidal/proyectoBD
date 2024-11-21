<?php
session_start();

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
if (!$conn) {
    $e = oci_error();
    echo "Error de conexión: " . $e['message'];
    exit;
}

$error = ''; // Variable para almacenar el mensaje de error

// Verificar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta SQL para verificar el usuario y obtener su tipo
    $query = 'SELECT * FROM usuarios WHERE username = :username AND password = :password';
    $stid = oci_parse($conn, $query);

    // Vincular parámetros
    oci_bind_by_name($stid, ':username', $username);
    oci_bind_by_name($stid, ':password', $password);

    // Ejecutar la consulta
    oci_execute($stid);

    // Verificar si se encontró el usuario
    if ($row = oci_fetch_array($stid, OCI_ASSOC)) {
        // Guardar información en la sesión
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['id_sesion'] = $row['ID_SESION']; // Aquí se guarda el tipo de usuario

        // Redirigir según el tipo de usuario
        if ($row['ID_SESION'] == 1) {
            // Administrador
            header("Location: empleados.php");
        } elseif ($row['ID_SESION'] == 2) {
            // Usuario regular
            header("Location: usuarios.php");
        } else {
            // Caso de error si el ID_SESION no es reconocido
            $error = "Tipo de usuario no válido.";
        }
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos."; // Mensaje de error si las credenciales no coinciden
    }
}
    oci_free_statement($stid);

// Cerrar conexión
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <style>
        /* Estilos generales */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        /* Estilo para el mensaje de error */
        .error {
            color: red;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <!-- Mostrar mensaje de error si existe -->
        <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
