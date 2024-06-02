<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

require_once "conexion.php";

$notificacion_id = $_POST['notificacion_id'];
$accion = $_POST['accion'];

$query_notificacion = "SELECT * FROM notificaciones WHERE ID = ?";
$statement_notificacion = $conexion->prepare($query_notificacion);
$statement_notificacion->bind_param("i", $notificacion_id);
$statement_notificacion->execute();
$result_notificacion = $statement_notificacion->get_result();

if ($result_notificacion->num_rows == 1) {
    $row_notificacion = $result_notificacion->fetch_assoc();
    $id_remitente = $row_notificacion['ID_Remitente'];
    $id_destinatario = $row_notificacion['ID_Destinatario'];
    $id_turno_interesado = $row_notificacion['ID_Turno_Interesado'];
    $id_turno_solicitado = $row_notificacion['ID_Turno_Solicitado'];
} else {
    echo "Error: No se encontró la notificación.";
    exit();
}

if ($accion == 'aceptar') {
    $conexion->begin_transaction();

    try {
        $query_intercambio = "UPDATE asignacion_turno 
                              SET ID_Trabajador = CASE ID_Trabajador 
                                                    WHEN ? THEN ? 
                                                    WHEN ? THEN ? 
                                                  END 
                              WHERE (ID_Turno = ? OR ID_Turno = ?) AND (ID_Trabajador = ? OR ID_Trabajador = ?)";

        $statement_intercambio = $conexion->prepare($query_intercambio);
        $statement_intercambio->bind_param("iiiiiiii", $id_remitente, $id_destinatario, $id_destinatario, $id_remitente, $id_turno_interesado, $id_turno_solicitado, $id_remitente, $id_destinatario);

        if (!$statement_intercambio->execute()) {
            throw new Exception("Error al realizar el intercambio de turno.");
        }

        $query_update_publicado_libre = "UPDATE asignacion_turno SET Publicado_libre = 0 WHERE ID_Turno IN (?, ?)";
        $statement_update_publicado_libre = $conexion->prepare($query_update_publicado_libre);
        $statement_update_publicado_libre->bind_param("ii", $id_turno_interesado, $id_turno_solicitado);

        if (!$statement_update_publicado_libre->execute()) {
            throw new Exception("Error al actualizar el estado de 'Publicado_libre'.");
        }

        $query_update = "UPDATE notificaciones SET Estado_notificacion = 'Aceptada' WHERE ID = ?";
        $statement_update = $conexion->prepare($query_update);
        $statement_update->bind_param("i", $notificacion_id);
        if (!$statement_update->execute()) {
            throw new Exception("Error al actualizar el estado de la notificación.");
        }

        $query_insert_valoracion = "INSERT INTO notificaciones (Tipo_notificacion, ID_Remitente, ID_Destinatario, ID_Turno_Interesado, ID_Turno_Solicitado, Estado_notificacion) 
                                    VALUES (?, ?, ?, ?, ?, 'Pendiente')";
        $statement_insert_valoracion = $conexion->prepare($query_insert_valoracion);
        $statement_insert_valoracion->bind_param("siiii", $tipo_notificacion_valoracion, $id_remitente, $id_destinatario, $id_turno_interesado, $id_turno_solicitado);
        $tipo_notificacion_valoracion = "Valoraciones";
        if (!$statement_insert_valoracion->execute()) {
            throw new Exception("Error al insertar la notificación de valoración.");
        }

        $conexion->commit();

        echo "Intercambio de turno realizado correctamente.";
    } catch (Exception $e) {
        $conexion->rollback();
        echo $e->getMessage();
    }
} elseif ($accion == 'rechazar') {
    $query_update = "UPDATE notificaciones SET Estado_notificacion = 'Rechazada' WHERE ID = ?";
    $statement_update = $conexion->prepare($query_update);
    $statement_update->bind_param("i", $notificacion_id);
    $statement_update->execute();

    echo "Solicitud de intercambio rechazada.";
}

header("Location: notificaciones.php");
exit();
?>
