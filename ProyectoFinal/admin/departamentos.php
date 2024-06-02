<?php
require_once "../conexion.php"; 
session_start();

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
    die("La conexión ha fallado: " . mysqli_connect_error());
}

function obtenerDepartamentos($conexion) {
    $sql = "SELECT * FROM departamentos";
    $resultado = mysqli_query($conexion, $sql);

    $departamentos = array();

    if (mysqli_num_rows($resultado) > 0) {
        while($fila = mysqli_fetch_assoc($resultado)) {
            $departamentos[] = $fila;
        }
    }

    return $departamentos;
}


function eliminarDepartamento($conexion, $id) {
    $sql1 = "DELETE FROM asignacion_turno WHERE ID_Trabajador IN (
                SELECT ID FROM trabajador WHERE Numero_Departamento = ?
             )";
    $stmt1 = mysqli_prepare($conexion, $sql1);
    mysqli_stmt_bind_param($stmt1, "i", $id);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);

    $sql2 = "DELETE FROM departamentos WHERE ID = ?";
    $stmt2 = mysqli_prepare($conexion, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
}


function nombreDepartamentoExistente($conexion, $nombre, $id) {
    $sql = "SELECT ID FROM departamentos WHERE Nombre = ? AND ID != ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "si", $nombre, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $count = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
    return $count > 0;
}

function modificarDepartamento($conexion, $id, $nombre, $descripcion) {
    if (empty($nombre) || empty($descripcion)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        if (nombreDepartamentoExistente($conexion, $nombre, $id)) {
            $error = "El nombre del departamento ya existe. Por favor, elige otro nombre.";
        } else {
            $sql = "UPDATE departamentos SET Nombre = ?, Descripcion = ? WHERE ID = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $nombre, $descripcion, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            header("Location: departamentos.php");
            exit();
        }
    }
}


if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    eliminarDepartamento($conexion, $id);
}

if (isset($_POST['modificar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    modificarDepartamento($conexion, $id, $nombre, $descripcion);
}

$departamentos = obtenerDepartamentos($conexion);

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
            return confirm("¿Estás seguro de que deseas eliminar este departamento?");
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
    <h2>Sistema de gestión de alta de departamentos</h2><br>
    <div class="descripcion-turnos">
        <p>Utilice el siguiente formulario para dar de alta, modificar o eliminar los departamentos. Para dar de alta un nuevo departamento, pulsa en el botón inferior.</p>
    </div><br>
    <div>
        <?php foreach ($departamentos as $departamento): ?>
            <br>
            <form action="" method="post" class="form-container-dep">
                <div class="input-group-dep">
                    <input type="hidden" name="id" value="<?php echo $departamento['ID']; ?>">
                    <label for="nombre-<?php echo $departamento['ID']; ?>">Nombre:</label>
                    <input type="text" id="nombre-<?php echo $departamento['ID']; ?>" name="nombre" value="<?php echo $departamento['Nombre']; ?>" required>
                </div>
                <div class="input-group-dep">
                    <label for="descripcion-<?php echo $departamento['ID']; ?>">Descripción:</label>
                    <textarea id="descripcion-<?php echo $departamento['ID']; ?>" name="descripcion" rows="4" required><?php echo $departamento['Descripcion']; ?></textarea>
                </div>
                <div class="button-container">
                    <button type="submit" name="modificar" class="n-boton-dep">Modificar</button>
                    <button type="submit" name="eliminar" class="n-boton-dep" onclick="return confirmarEliminacion();">Eliminar</button>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
    <div class="botones-container">
        <a href="crearDepartamento.php" class="n-boton">Crear Nuevo Departamento</a>
    </div>
</section>
</body>
</html>
