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

// Consulta SQL para obtener empleados con sus datos
$query = "SELECT E.ID_EMPLEADO, E.NOMBRE_EMPLEADO, E.APELLIDO1_EMPLEADO, E.APELLIDO2_EMPLEADO, 
                 A.NOMBRE_AREA_TRABAJO, S.NOMBRE_SUCURSAL
          FROM BC_BR_BV_RM_EMPLEADOS E
          LEFT JOIN BC_BR_BV_RM_AREA_TRABAJO A ON E.ID_AREA_TRABAJO = A.ID_AREA_TRABAJO
          LEFT JOIN BC_BR_BV_RM_SUCURSAL S ON E.ID_SUCURSAL = S.ID_SUCURSAL
          ORDER BY S.NOMBRE_SUCURSAL ASC, A.NOMBRE_AREA_TRABAJO ASC";

$stid = oci_parse($conn, $query);
if (!$stid) {
    $e = oci_error($conn);
    die("Error al preparar la consulta: " . $e['message']);
}

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("Error al ejecutar la consulta: " . $e['message']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Empleados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 20px auto;
            width: 80%;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-danger{
            background-color: #ff3333;
        }
        .btn-danger:hover{
            background-color: #ab0e0e;  
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Empleados</h1>
        <a href="agregar_empleados.php" class="btn">Nuevo Empleado</a>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellido Paterno</th>
                    <th>Apellido Materno</th>
                    <th>Área de Trabajo</th>
                    <th>Sucursal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $hasResults = false; // Bandera para comprobar si hay resultados
                    while ($row = oci_fetch_assoc($stid)) {
                        $hasResults = true;
                        echo "<tr>";
                        echo "<td>" . htmlentities($row['NOMBRE_EMPLEADO']) . "</td>";
                        echo "<td>" . htmlentities($row['APELLIDO1_EMPLEADO']) . "</td>";
                        echo "<td>" . htmlentities($row['APELLIDO2_EMPLEADO']) . "</td>";
                        echo "<td>" . htmlentities($row['NOMBRE_AREA_TRABAJO']) . "</td>";
                        echo "<td>" . htmlentities($row['NOMBRE_SUCURSAL']) . "</td>";
                        echo "<td>
                                <a href='editar_empleado.php?id=" . $row['ID_EMPLEADO'] . "' class='btn btn-primary'>Editar</a>
                                <a href='eliminar_empleado.php?id=" . $row['ID_EMPLEADO'] . "' class='btn btn-danger' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este empleado?');\">Eliminar</a>
                            </td>";
                        echo "</tr>";
                    }
                    if (!$hasResults) {
                        echo "<tr><td colspan='6'>No hay empleados registrados</td></tr>";
                    }
                ?>
        </tbody>

        </table>
    </div>
</body>
</html>

<?php
// Liberar recursos y cerrar la conexión
oci_free_statement($stid);
oci_close($conn);
?>
