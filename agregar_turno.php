<?php
if (!isset($_GET['id_empleado'])) {
    die("ID del empleado no proporcionado.");
}

$id_empleado = $_GET['id_empleado'];

session_start();
include('navbar.php');
// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', '26.179.117.214/XEPDB1');
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
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/agregarTurno.css">
</head>
<body>
    <img src="img/fondo2.jpeg" alt="Fondo" class="background">
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
