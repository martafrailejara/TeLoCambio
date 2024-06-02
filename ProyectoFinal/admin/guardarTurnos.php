<?php
require_once "../conexion.php";
session_start();

if (isset($_POST['guardar'])) {
    $departamento_id = $_POST['departamento_id'];

    foreach (['mañana', 'tarde', 'noche'] as $turno) {
        foreach (['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'] as $dia) {
            $trabajador_id = $_POST["trabajador_$turno" . "_$dia"];

            $sql_check = "SELECT ID FROM asignacion_turno 
                          WHERE ID_Departamento = ? 
                          AND ID_Turno = (SELECT ID FROM turno WHERE Tipo_turno = ? AND Dia_semana = ?)";
            $stmt_check = mysqli_prepare($conexion, $sql_check);
            mysqli_stmt_bind_param($stmt_check, "iss", $departamento_id, $turno, $dia);
            mysqli_stmt_execute($stmt_check);
            $resultado_check = mysqli_stmt_get_result($stmt_check);

            if (mysqli_num_rows($resultado_check) > 0) {
                $fila = mysqli_fetch_assoc($resultado_check);
                $asignacion_id = $fila['ID'];
                $sql_update = "UPDATE asignacion_turno SET ID_Trabajador = ?, Publicado_libre = 0 WHERE ID = ?";
                $stmt_update = mysqli_prepare($conexion, $sql_update);
                mysqli_stmt_bind_param($stmt_update, "ii", $trabajador_id, $asignacion_id);
                mysqli_stmt_execute($stmt_update);
            } else {
                $sql_insert = "INSERT INTO asignacion_turno (ID_Departamento, ID_Turno, ID_Trabajador, Publicado_libre) 
                               VALUES (?, (SELECT ID FROM turno WHERE Tipo_turno = ? AND Dia_semana = ?), ?, 0)";
                $stmt_insert = mysqli_prepare($conexion, $sql_insert);
                mysqli_stmt_bind_param($stmt_insert, "isii", $departamento_id, $turno, $dia, $trabajador_id);
                mysqli_stmt_execute($stmt_insert);
            }

            mysqli_stmt_close($stmt_check);
        }
    }

    header("Location: crearTurnos.php");
    exit();
} else {
}
?>
