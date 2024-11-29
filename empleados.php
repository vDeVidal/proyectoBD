<<<<<<< HEAD
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
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/empleados.css">
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

=======
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
    <link rel="icon" href="img/icono.jpeg.jpg">
    <link rel="stylesheet" href="css/empleados.css">
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

>>>>>>> 857b980ee93fe07db56e79d544d54dd372ae7124
