<?php
// Iniciar sesión para identificar al usuario
session_start();
$id_empleado = $_SESSION['id_empleado']; // ID del usuario autenticado

// Consultar información del empleado
$sql_empleado = "SELECT * FROM BC_BR_BV_RM_EMPLEADOS WHERE ID_EMPLEADO = $id_empleado";
$result_empleado = $conn->query($sql_empleado);
$empleado = $result_empleado->fetch_assoc();

// Consultar turnos y horas trabajadas
$sql_turnos = "SELECT T.ID_TURNO, HI.HORA_INGRESO, HS.HORA_SALIDA 
               FROM BC_BR_BV_RM_TURNOS T
               JOIN BC_BR_BV_RM_HORA_INGRESO HI ON T.ID_HORA_INGRESO = HI.ID_HORA_INGRESO
               JOIN BC_BR_BV_RM_HORA_SALIDA HS ON T.ID_HORA_SALIDA = HS.ID_HORA_SALIDA
               WHERE T.ID_TURNO = " . $empleado['ID_TURNO'];
$result_turnos = $conn->query($sql_turnos);

// Consultar destinatarios para mensajes
$sql_destinatarios = "SELECT ID_EMPLEADO, NOMBRE_EMPLEADO FROM BC_BR_BV_RM_EMPLEADOS";
$result_destinatarios = $conn->query($sql_destinatarios);

// Procesar envío de mensajes si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinatario = $_POST['destinatario'];
    $mensaje = $_POST['mensaje'];
    $sql_mensaje = "INSERT INTO mensajes (remitente, destinatario, mensaje, fecha_envio) 
                    VALUES ($id_empleado, $destinatario, '$mensaje', NOW())";

    if ($conn->query($sql_mensaje) === TRUE) {
        $mensaje_exito = "Mensaje enviado correctamente.";
    } else {
        $mensaje_error = "Error al enviar el mensaje: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Usuario</title>
    <link rel="stylesheet" href="styles.css"> <!-- Opcional -->
</head>
<body>
    <header>
        <h1>Bienvenido a tu Panel</h1>
    </header>
    <main>
        <!-- Información del empleado -->
        <section>
            <h2>Información Personal</h2>
            <p>Nombre: <?php echo $empleado['NOMBRE_EMPLEADO']; ?></p>
            <p>Teléfono: <?php echo $empleado['NUMERO_TELEFONO']; ?></p>
            <p>Sueldo: <?php echo $empleado['SUELDO']; ?></p>
            <p>Horas Extras: <?php echo $empleado['HORAS_EXTRAS']; ?></p>
        </section>

        <!-- Turnos y horas trabajadas -->
        <section>
            <h2>Reporte de Horas</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Hora de Ingreso</th>
                        <th>Hora de Salida</th>
                        <th>Horas Trabajadas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_turnos->num_rows > 0) {
                        while ($row = $result_turnos->fetch_assoc()) {
                            $horas_trabajadas = $row['HORA_SALIDA'] - $row['HORA_INGRESO'];
                            echo "<tr>
                                <td>" . date('Y-m-d') . "</td>
                                <td>" . $row['HORA_INGRESO'] . "</td>
                                <td>" . $row['HORA_SALIDA'] . "</td>
                                <td>" . $horas_trabajadas . " horas</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No se encontraron turnos asignados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>

        <!-- Mensajería -->
        <section>
            <h2>Mensajes</h2>
            <?php
            if (isset($mensaje_exito)) {
                echo "<p style='color: green;'>$mensaje_exito</p>";
            } elseif (isset($mensaje_error)) {
                echo "<p style='color: red;'>$mensaje_error</p>";
            }
            ?>
            <form action="" method="POST">
                <label for="destinatario">Destinatario:</label>
                <select name="destinatario" id="destinatario" required>
                    <?php
                    if ($result_destinatarios->num_rows > 0) {
                        while ($row = $result_destinatarios->fetch_assoc()) {
                            echo "<option value='" . $row['ID_EMPLEADO'] . "'>" . $row['NOMBRE_EMPLEADO'] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay destinatarios disponibles</option>";
                    }
                    ?>
                </select>
                <br>
                <label for="mensaje">Mensaje:</label>
                <textarea name="mensaje" id="mensaje" rows="5" required></textarea>
                <br>
                <button type="submit">Enviar</button>
            </form>
        </section>
    </main>
</body>
</html>
