<?php
require_once "../conexion.php";
session_start();

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
    die("La conexión ha fallado: " . mysqli_connect_error());
}

$errorMessage = '';

function existeDepartamento($conexion, $nombre) {
    $sql = "SELECT ID FROM departamentos WHERE Nombre = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "s", $nombre);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $filas = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
    return $filas > 0;
}

function crearDepartamento($conexion, $nombre, $descripcion) {
    if (existeDepartamento($conexion, $nombre)) {
        global $errorMessage;
        $errorMessage = "El nombre del departamento ya existe. Por favor, elige otro nombre.";
    } else {
        $sql = "INSERT INTO departamentos (Nombre, Descripcion) VALUES (?, ?)";
        $stmt = mysqli_prepare($conexion, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $nombre, $descripcion);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: departamentos.php");
        exit();
    }
}

if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    crearDepartamento($conexion, $nombre, $descripcion);
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
    <style>
        .error-message {
            color: red;
        }
    </style>
</head>
<body class="alta">
<?php include 'n-barAdmin.php'; ?>
<section class="home-section">
    <div class="encabezado">
        <img src="/img/descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo alta">Menú del Administrador</h1>
    </div>
    <h2>Sistema de gestión de alta de departamentos</h2><br>
    <div class="descripcion-turnos">
        <p>Utilice el siguiente formulario para dar de alta nuevos departamentos. Para volver a la modificación o eliminación puedes pulsar el botón inferior.</p>
    </div><br><br>
    <div class="form-container-dep">
        <form action="" method="post">
            <div class="input-group-dep">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="input-group-dep">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>
            <p class="error-message"><?php echo $errorMessage; ?><br></p>
            <br><button type="submit" name="crear" class="n-boton-dep">Crear Departamento</button>
        </form>
    </div>
    <div class="botones-container">
        <a href="departamentos.php" class="n-boton">Volver a los departamentos</a>
    </div>
</section>
</body>
</html>
