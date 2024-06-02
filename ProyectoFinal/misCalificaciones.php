<?php 
require_once "conexion.php";

session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION["usuario"];
$query_usuario_id = "SELECT ID FROM trabajador WHERE Nombre_Usuario = ?";
$statement_usuario_id = $conexion->prepare($query_usuario_id);

if (!$statement_usuario_id) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$statement_usuario_id->bind_param("s", $usuario);
$statement_usuario_id->execute();
$result_usuario_id = $statement_usuario_id->get_result();

if (!$result_usuario_id) {
    die("Error al ejecutar la consulta: " . $conexion->error);
}

if ($result_usuario_id->num_rows == 1) {
    $row_usuario_id = $result_usuario_id->fetch_assoc();
    $destinatario_id = $row_usuario_id['ID'];

    $query_valoraciones = "SELECT v.Puntuacion, v.Comentario, t.Nombre, t.Apellido
                           FROM valoraciones v
                           INNER JOIN trabajador t ON v.ID_Valorador = t.ID
                           WHERE v.ID_Trabajador_Valorado = ?";
    $statement_valoraciones = $conexion->prepare($query_valoraciones);

    if (!$statement_valoraciones) {
        die("Error en la preparación de la consulta: " . $conexion->error);
    }

    $statement_valoraciones->bind_param("i", $destinatario_id);
    $statement_valoraciones->execute();
    $result_valoraciones = $statement_valoraciones->get_result();

    if (!$result_valoraciones) {
        die("Error al ejecutar la consulta: " . $conexion->error);
    }
} else {
    echo "<p>Error: No se pudo obtener el ID del usuario.</p>";
}


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorar Servicio</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<?php include 'n-bar.php'; ?>
<section class="home-section">
    <div class="encabezado">
        <img src="\img\descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo">TeLoCambio</h1>
        <p class="slogan">¡Cambiando tu experiencia!</p>
    </div>
    <h2>Mis calificaciones</h2><br>
    <div class="descripcion-turnos">
      <p>Estas son las valoraciones de los turnos que has completado a otras personas mediante intercambios</p>
    </div><br>
    <div>
        <?php
        if ($result_valoraciones->num_rows > 0) {
            while ($row_valoracion = $result_valoraciones->fetch_assoc()) {
                $puntuacion = $row_valoracion['Puntuacion'];
                $comentario = $row_valoracion['Comentario'];
                $nombre_Valorador = $row_valoracion['Nombre'];
                $apellido_Valorador = $row_valoracion['Apellido'];

                echo "<div class='turno-calificaciones'>";
                    echo "<p class='nombre-calificaciones'>Valorado por: <span class='valorador-nombre'>$nombre_Valorador $apellido_Valorador</span></p>";
                    echo "<p><br>";
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $puntuacion) {
                            echo "<i class='bx bxs-star estrellas-calificaciones amarillo'></i>";
                        } else {
                            echo "<i class='bx bx-star estrellas-calificaciones'></i>";
                        }
                    }
                    echo "</p>";
                    if (!empty($comentario)) {
                        echo "<p> <br>Comentario: <span class='comentario-calificaciones'>$comentario</span></p>";
                    }
                echo "</div><br>";
            }
        } else {
            echo "<p class='turno-calificaciones'>No se han recibido valoraciones.</p>";
        }
        ?>
    </div>
</section>
</body>
</html>
