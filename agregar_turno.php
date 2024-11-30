<?php
if (!isset($_GET['id_empleado'])) {
    die("ID del empleado no proporcionado.");
}

$id_empleado = $_GET['id_empleado'];

session_start();

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Turno</title>
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
        <h2>Agregar Turno</h2>
        <form method="POST" action="procesar_turno.php">
            <input type="hidden" name="id_empleado" value="<?php echo $id_empleado; ?>">
            <div class="form-group">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
            </div>
            <div class="form-group">
                <label for="hora_ingreso">Hora de Ingreso:</label>
                <input type="time" id="hora_ingreso" name="hora_ingreso" required>
            </div>
            <div class="form-group">
                <label for="hora_salida">Hora de Salida:</label>
                <input type="time" id="hora_salida" name="hora_salida" required>
            </div>
            <div class="form-group">
                <button type="submit">Guardar Turno</button>
            </div>
        </form>
    </div>
</body>
</html>
