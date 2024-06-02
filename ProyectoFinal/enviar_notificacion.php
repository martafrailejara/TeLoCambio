<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

require_once "conexion.php";

$notificacion = "";

if (isset($_SESSION["usuario"])) {
    $usuario = $_SESSION["usuario"];
    $query_usuario = "SELECT id, numero_departamento FROM trabajador WHERE nombre_usuario = ?";
    $statement_usuario = $conexion->prepare($query_usuario);
    $statement_usuario->bind_param("s", $usuario);
    $statement_usuario->execute();
    $result_usuario = $statement_usuario->get_result();

    if ($result_usuario->num_rows == 1) {
        $row_usuario = $result_usuario->fetch_assoc();
        $trabajador_id_remitente = $row_usuario['id'];
        $departamento_remitente = $row_usuario['numero_departamento'];
    } else {
        $notificacion = "Error: No se encontró el trabajador actual.";
    }
} else {
    $notificacion = "Error: No se ha iniciado sesión.";
}

if (isset($_POST['turno_id'])) {
    $turno_id_interesado = $_POST['turno_id'];
} else {
    $notificacion = "Error: No se encontró el ID del turno interesado en la URL.";
}

if (isset($_POST['trabajador_id'])) {
    $trabajador_id_destinatario = $_POST['trabajador_id'];
} else {
    $notificacion = "Error: No se encontró el ID del trabajador destinatario en la URL.";
}

if (isset($_POST['turno_id_url']) && isset($_POST['trabajador_id_url'])) {
    $turno_id_solicitado = $_POST['turno_id_url'];
    $trabajador_id_solicitante = $_POST['trabajador_id_url'];
} else {
    $notificacion = "Error: No se encontraron los datos de la URL.";
}

if (empty($notificacion)) {
    $query_check = "SELECT * FROM notificaciones 
                    WHERE id_remitente = ? 
                      AND (id_turno_interesado = ? OR id_turno_solicitado = ?)
                      AND estado_notificacion = 'Pendiente'";
    $statement_check = $conexion->prepare($query_check);
    $statement_check->bind_param("iii", $trabajador_id_remitente, $turno_id_interesado, $turno_id_solicitado);
    $statement_check->execute();
    $result_check = $statement_check->get_result();

    if ($result_check->num_rows > 0) {
        $notificacion = "Ya has enviado una solicitud de intercambio para este turno o para esta persona que está pendiente.";
    }
}

if (empty($notificacion)) {
    $query_check = "SELECT * FROM notificaciones 
                    WHERE id_turno_interesado = ?
                      AND id_destinatario = ?
                      AND estado_notificacion = 'Pendiente'";
    $statement_check = $conexion->prepare($query_check);
    $statement_check->bind_param("ii", $turno_id_interesado, $trabajador_id_destinatario);
    $statement_check->execute();
    $result_check = $statement_check->get_result();

    if ($result_check->num_rows > 0) {
        $notificacion = "El destinatario ya tiene una solicitud pendiente para este turno. Debe esperar a que este responda a esta solicitud antes de enviar una nueva.";
    }
}

if (empty($notificacion)) {
    $query_insert = "INSERT INTO notificaciones (tipo_notificacion, id_remitente, id_destinatario, id_turno_solicitado, id_turno_interesado, estado_notificacion) 
                     VALUES ('Solicitud de intercambio', ?, ?, ?, ?, 'Pendiente')";

    $statement_insert = $conexion->prepare($query_insert);
    $statement_insert->bind_param("iiii", $trabajador_id_remitente, $trabajador_id_destinatario, $turno_id_solicitado, $turno_id_interesado);

    if ($statement_insert->execute()) {
        $notificacion = "Notificación enviada correctamente.";
    } else {
        $notificacion = "Error al enviar la notificación.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeLoCambio Administrador</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="alta">
<?php include 'n-bar.php'; ?> 
<section class="home-section">
    <div class="encabezado">
        <img src="\img\descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo">TeLoCambio</h1>
        <p class="slogan">¡Cambiando tu experiencia!</p>
    </div>
    <h2>Solicitud de intercambio</h2><br>
    <div class="turno-opciones-container">
        <div id="notificaciones">
            <?php 
            echo $notificacion; 
            if (strpos($notificacion, "Notificación enviada correctamente") !== false) {
                echo '<br><div class="icono-tic"><i class="bx bx-check-circle"></i></div>';
            } else {
                echo '<br><div class="icono-x"><i class="bx bx-x-circle"></i></div>';
            }
            ?>
        </div>
    </div>
    <div class="botones-container">
        <a href="misTurnos.php" class="n-boton">VOLVER</a>
    </div>
</section>
</body>
</html>
