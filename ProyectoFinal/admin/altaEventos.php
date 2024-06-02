<?php
require_once "../conexion.php";
session_start();

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
    die("La conexión ha fallado: " . mysqli_connect_error());
}

function crearEvento($conexion, $fecha, $descripcion) {
    $sql = "INSERT INTO eventos (Fecha, Descripcion) VALUES (?, ?)";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $fecha, $descripcion);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

if (isset($_POST['crear'])) {
    $fecha = $_POST['fecha'];
    $descripcion = $_POST['descripcion'];
    crearEvento($conexion, $fecha, $descripcion);
    header("Location: gestorEventos.php");
    exit();
}

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
</head>
<body class="alta">
<?php include 'n-barAdmin.php'; ?> 
<section class="home-section">
    <div class="encabezado">
        <img src="/img/descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo alta">Menú del Administrador</h1>
    </div>
    <h2>Sistema de gestión de alta de eventos</h2><br>
    <div class="descripcion-turnos">
        <p>Utilice el siguiente formulario para dar de alta nuevos eventos. Para volver a la modificación o eliminación, puedes pulsar el botón inferior.</p>
    </div><br><br>
    <div class="form-container-dep">
        <form action="altaEventos.php" method="post">
            <div class="input-group-dep">
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="input-group-dep">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>
            <button type="submit" name="crear" class="n-boton-dep">Crear Evento</button>
        </form>
    </div>
    <div class="botones-container">
        <a href="gestorEventos.php" class="n-boton">Volver a los eventos</a>
    </div>
</section>
</body>
</html>
