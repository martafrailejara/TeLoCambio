<?php
require_once "../conexion.php"; 
session_start();

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
    die("La conexión ha fallado: " . mysqli_connect_error());
}

function obtenerEventos($conexion) {
    $sql = "SELECT * FROM eventos";
    $resultado = mysqli_query($conexion, $sql);

    $eventos = array();

    if (mysqli_num_rows($resultado) > 0) {
        while($fila = mysqli_fetch_assoc($resultado)) {
            $eventos[] = $fila;
        }
    }

    return $eventos;
}

function eliminarEvento($conexion, $id) {
    $sql = "DELETE FROM eventos WHERE ID = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function modificarEvento($conexion, $id, $fecha, $descripcion) {
    $sql = "UPDATE eventos SET Fecha = ?, Descripcion = ? WHERE ID = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $fecha, $descripcion, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    eliminarEvento($conexion, $id);
}

if (isset($_POST['modificar'])) {
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    modificarEvento($conexion, $id, $fecha, $descripcion);
}

$eventos = obtenerEventos($conexion);

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
            return confirm("¿Estás seguro de que deseas eliminar este evento?");
        }
    </script>
</head>
<body>
<?php include 'n-barAdmin.php'; ?>
<section class="home-section">
    <div class="encabezado">
        <img src="/img/descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo alta">Menú del Administrador</h1>
    </div>
    <h2>Sistema de gestión de eventos</h2><br>
    <div class="descripcion-turnos">
        <p>Utilice el siguiente formulario para agregar, modificar o eliminar eventos. Para agregar un nuevo evento, haga clic en el botón inferior.</p>
    </div><br>
    <div>
        <?php foreach ($eventos as $evento): ?>
            <br>
            <form action="" method="post" class="form-container-dep">
                <div class="input-group-dep">
                    <input type="hidden" name="id" value="<?php echo $evento['ID']; ?>">
                    <label for="fecha-<?php echo $evento['ID']; ?>">Fecha:</label>
                    <input type="date" id="fecha-<?php echo $evento['ID']; ?>" name="fecha" value="<?php echo $evento['Fecha']; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="input-group-dep">
                    <label for="descripcion-<?php echo $evento['ID']; ?>">Descripción:</label>
                    <textarea id="descripcion-<?php echo $evento['ID']; ?>" name="descripcion" rows="4" required><?php echo $evento['Descripcion']; ?></textarea>
                </div>
                <div class="button-container">
                    <button type="submit" name="modificar" class="n-boton-dep">Modificar</button>
                    <button type="submit" name="eliminar" class="n-boton-dep" onclick="return confirmarEliminacion();">Eliminar</button>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
    <div class="botones-container">
        <a href="altaEventos.php" class="n-boton">Agregar Nuevo Evento</a>
    </div>
</section>
</body>
</html>
