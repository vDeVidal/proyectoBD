<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Conexión a la base de datos
$conn = oci_connect('benja', 'benja123', 'localhost/XEPDB1');
if (!$conn) {
    $e = oci_error();
    die("Error de conexión: " . $e['message']);
}

// Filtrar por nombre si se proporcionó en la búsqueda
$nombre_filtro = isset($_GET['nombre']) ? $_GET['nombre'] : '';

$query = "SELECT E.ID_EMPLEADO, E.NOMBRE_EMPLEADO, E.APELLIDO1_EMPLEADO, E.APELLIDO2_EMPLEADO,
                 A.NOMBRE_AREA_TRABAJO, S.NOMBRE_SUCURSAL
          FROM BC_BR_BV_RM_EMPLEADOS E
          LEFT JOIN BC_BR_BV_RM_AREA_TRABAJO A ON E.ID_AREA_TRABAJO = A.ID_AREA_TRABAJO
          LEFT JOIN BC_BR_BV_RM_SUCURSAL S ON E.ID_SUCURSAL = S.ID_SUCURSAL";
          
if (!empty($nombre_filtro)) {
    $query .= " WHERE LOWER(E.NOMBRE_EMPLEADO) LIKE LOWER(:nombre)";
}
$query .= " ORDER BY S.NOMBRE_SUCURSAL, A.NOMBRE_AREA_TRABAJO";

$stid = oci_parse($conn, $query);

if (!empty($nombre_filtro)) {
    $nombre_filtro = '%' . $nombre_filtro . '%';
    oci_bind_by_name($stid, ':nombre', $nombre_filtro);
}

oci_execute($stid);
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
        .actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .actions .btn {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            color: white;
            background-color: #007bff;
        }
        .actions .btn:hover {
            background-color: #0056b3;
        }
        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-form input {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
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
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            color: white;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-edit:hover {
            background-color: #0056b3;
        }
        .btn-turno {
            background-color: #258282;
          
        }
        .btn-turno:hover {
            background-color: #17a2b8;
          
        }
        .btn-verTurno{
            background-color: #28a745;
           
        }
        .btn-verTurno:hover{
            background-color:#218838;
          
        }
        .btn-delete {
            background-color: #ff3333;
        }
        .btn-delete:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Empleados</h1>
        <div class="actions">
            <form class="search-form" method="GET" action="">
                <input type="text" name="nombre" placeholder="Buscar por nombre" value="<?php echo htmlspecialchars(isset($_GET['nombre']) ? $_GET['nombre'] : ''); ?>">
                <button type="submit" class="btn">Buscar</button>
            </form>
            <a href="agregar_empleado.php" class="btn">Nuevo Empleado</a>
        </div>
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
                while ($row = oci_fetch_assoc($stid)) {
                    echo "<tr>";
                    echo "<td>" . htmlentities($row['NOMBRE_EMPLEADO']) . "</td>";
                    echo "<td>" . htmlentities($row['APELLIDO1_EMPLEADO']) . "</td>";
                    echo "<td>" . htmlentities($row['APELLIDO2_EMPLEADO']) . "</td>";
                    echo "<td>" . htmlentities($row['NOMBRE_AREA_TRABAJO']) . "</td>";
                    echo "<td>" . htmlentities($row['NOMBRE_SUCURSAL']) . "</td>";
                    echo "<td>";
                    echo "<a href='editar_empleado.php?id=" . $row['ID_EMPLEADO'] . "' class='btn btn-edit'>Editar</a> ";
                    echo "<a href='agregar_turno.php?id_empleado=" . $row['ID_EMPLEADO'] . "' class='btn btn-turno'>Agregar Turno</a> ";
                    echo "<a href='ver_turnos.php?id=" . $row['ID_EMPLEADO'] . "' class='btn btn-verTurno'>Ver Turnos</a> ";
                    echo "<a href='eliminar_empleado.php?id=" . $row['ID_EMPLEADO'] . "' class='btn btn-delete'>Eliminar</a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

