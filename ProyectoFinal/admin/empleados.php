<?php
require_once "../conexion.php"; 
session_start();

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
    die("La conexión ha fallado: " . mysqli_connect_error());
}

function obtenerEmpleados($conexion, $busqueda = '') {
    $sql = "SELECT * FROM trabajador";
    if ($busqueda) {
        $busqueda = mysqli_real_escape_string($conexion, $busqueda);
        $sql .= " WHERE Nombre LIKE '%$busqueda%' OR Apellido LIKE '%$busqueda%'";
    }
    $sql .= " ORDER BY Nombre, Apellido";
    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        die("Error en la consulta: " . mysqli_error($conexion));
    }

    $empleados = array();

    if (mysqli_num_rows($resultado) > 0) {
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $empleados[] = $fila;
        }
    }

    return $empleados;
}

function eliminarEmpleado($conexion, $id) {
    $sql = "DELETE FROM trabajador WHERE ID = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    eliminarEmpleado($conexion, $id);
}

$busqueda = '';
if (isset($_GET['busqueda'])) {
    $busqueda = $_GET['busqueda'];
}

$empleados = obtenerEmpleados($conexion, $busqueda);
mysqli_close($conexion);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TeLoCambio Administrador</title>
<link rel="stylesheet" href="/css/estilosAdministrador.css">
<link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
<script>
function confirmarEliminacion() {
    return confirm("¿Estás seguro de que deseas eliminar este empleado?");
}

function validarBusqueda() {
    var busqueda = document.getElementById("busqueda").value;
    if (busqueda.length < 3) {
        alert("Por favor, ingrese al menos 3 caracteres para buscar.");
        return false;
    }
    return true;
}

function reiniciarBusqueda() {
    document.getElementById("busqueda").value = '';
    window.location.href = window.location.pathname; 
}
</script>
<style>

</style>
</head>
<body>
<?php include 'n-barAdmin.php'; ?> 
<section class="home-section">
    <div class="encabezado">
        <img src="/img/descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo alta">Menú del Administrador</h1>
    </div>
    <h2>Sistema de gestión de trabajadores</h2><br>
    <div class="descripcion-turnos">
        <p>Utilice el siguiente formulario para modificar o eliminar trabajadores existentes. Si desea agregar un nuevo trabajador, haga clic en el botón inferior.</p>
    </div><br><br>
    <div class="search-container-wrapper">
        <form method="get" action="" onsubmit="return validarBusqueda();" class="search-container">
            <input type="text" id="busqueda" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar...">
            <button type="submit">
                <i class='bx bx-search'></i> 
            </button>
            <button type="button" onclick="reiniciarBusqueda()">
                <i class='bx bx-x'></i>
            </button>
        </form>
    </div>
    <div>
    <?php if (count($empleados) > 0): ?>
        <?php foreach ($empleados as $empleado): ?>
            <br>
            <div class="form-container-dep">
                <span>
                    <?php echo '<strong>' . htmlspecialchars($empleado['Nombre'] . ' ' . $empleado['Apellido']) . '</strong> - ' . htmlspecialchars($empleado['Correo_electronico']) . ' - Nº Departamento:' . htmlspecialchars($empleado['Numero_Departamento']); ?>
                </span>
                <div class="botones-container2">
                    <form action="modificarEmpleado.php" method="get">
                        <input type="hidden" name="id" value="<?php echo $empleado['ID']; ?>">
                        <button type="submit" class="n-boton">Modificar</button>
                    </form>
                    <form action="" method="post" class="form-eliminar-trab" onsubmit="return confirmarEliminacion();">
                        <input type="hidden" name="id" value="<?php echo $empleado['ID']; ?>">
                        <button type="submit" name="eliminar" class="n-boton">Eliminar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="form-container-dep">No se han obtenido resultados</p>
    <?php endif; ?>
    </div>

    <div class="botones-container">
        <a href="altaUsuarios.php" class="n-boton">Dar de alta un nuevo empleado</a>
    </div>
</section>
</body>
</html>
