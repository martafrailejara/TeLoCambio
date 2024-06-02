<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["notificacion_id"], $_POST["valoracion"])) {
    $notificacion_id = $_POST["notificacion_id"];
    $valoracion = $_POST["valoracion"];
    $comentario = isset($_POST["comentario"]) ? $_POST["comentario"] : null; 

    $usuario = $_SESSION["usuario"];
    $query_usuario = "SELECT ID FROM trabajador WHERE Nombre_Usuario = ?";
    $statement_usuario = $conexion->prepare($query_usuario);
    $statement_usuario->bind_param("s", $usuario);
    $statement_usuario->execute();
    $result_usuario = $statement_usuario->get_result();

    if ($result_usuario->num_rows == 1) {
        $row_usuario = $result_usuario->fetch_assoc();
        $valorador_id = $row_usuario['ID'];
    } else {
        exit("Error: No se pudo obtener el ID del trabajador.");
    }

    $query_notificacion = "SELECT ID_Destinatario FROM notificaciones WHERE ID = ?";
    $statement_notificacion = $conexion->prepare($query_notificacion);
    $statement_notificacion->bind_param("i", $notificacion_id);
    $statement_notificacion->execute();
    $result_notificacion = $statement_notificacion->get_result();

    if ($result_notificacion->num_rows == 1) {
        $row_notificacion = $result_notificacion->fetch_assoc();
        $trabajador_valorado_id = $row_notificacion['ID_Destinatario'];
    } else {
        exit("Error: No se pudo obtener la ID del trabajador valorado.");
    }

$query_insert_valoracion = "INSERT INTO valoraciones (ID_Valorador, ID_Trabajador_Valorado, Puntuacion, Comentario) VALUES (?, ?, ?, ?)";
$statement_insert_valoracion = $conexion->prepare($query_insert_valoracion);
$statement_insert_valoracion->bind_param("iiis", $valorador_id, $trabajador_valorado_id, $valoracion, $comentario);

if ($statement_insert_valoracion->execute()) {
    $query_update_notificacion = "UPDATE notificaciones SET Estado_notificacion = 'Terminado' WHERE ID = ?";
    $statement_update_notificacion = $conexion->prepare($query_update_notificacion);
    $statement_update_notificacion->bind_param("i", $notificacion_id);
    $statement_update_notificacion->execute();

    header("Location: valoraciones.php?mensaje=¡Gracias+por+tu+valoración!");
    exit();
} else {
    header("Location: valoraciones.php?mensaje=Error+al+insertar+la+valoración:+".$conexion->error);
    exit();
}

}    
?>
