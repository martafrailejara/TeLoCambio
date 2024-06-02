<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

require_once "conexion.php";

setlocale(LC_TIME, 'es_ES.UTF-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos de Trabajo</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .campana {
            align-items: center;
            color: #0B3954;
            font-size: 4rem; 
            background-color: #BFD7EA;
            padding: 5px;
            border-radius: 20%;
        }

        .campana-container {
            display: flex;
            justify-content: center; 
            align-items: center;
            margin-bottom: 20px; 
        }

        .libre {
            color: green;
            font-weight: bold; 
        }
    </style>
</head>
<body>
<?php include 'n-bar.php'; ?>
<section class="home-section">
    <div class="encabezado">
        <img src="\img\descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo">TeLoCambio</h1>
        <p class="slogan">¡Cambiando tu experiencia!</p>
    </div>
    <h2>Estos son los turnos de esta semana</h2><br>
    <div class="descripcion-turnos">
        <p>¡Bienvenido/a! A continuación, encontrarás los turnos programados para esta semana. Por favor, revisa la tabla para ver los detalles.</p>
    </div><br>
    <?php
$query = "SELECT t.Tipo_turno, t.Dia_semana, 
                 GROUP_CONCAT(CASE WHEN at.Publicado_libre = 1 
                                   THEN CONCAT('<span class=\"libre\">', tr.Nombre, ' ', tr.Apellido, '</span>') 
                                   ELSE CONCAT(tr.Nombre, ' ', tr.Apellido) 
                                   END ORDER BY tr.ID SEPARATOR '<br>') AS nombres
          FROM turno t
          LEFT JOIN asignacion_turno at ON t.ID = at.ID_Turno
          LEFT JOIN trabajador tr ON at.ID_Trabajador = tr.ID
          GROUP BY t.Tipo_turno, t.Dia_semana
          ORDER BY FIELD(t.Tipo_turno, 'mañana', 'tarde', 'noche'), 
                   FIELD(t.Dia_semana, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo')";


    $result = $conexion->query($query);
    if ($result === false) {
        die('Error en la consulta SQL: ' . $conexion->error);
    }

    $tabla_turnos = array();

    while ($row = $result->fetch_assoc()) {
        $turno = $row['Tipo_turno'];
        $dia = $row['Dia_semana'];
        $nombres = $row['nombres'];

        if (!isset($tabla_turnos[$turno])) {
            $tabla_turnos[$turno] = array();
        }

        if (!isset($tabla_turnos[$turno][$dia])) {
            $tabla_turnos[$turno][$dia] = $nombres;
        } else {
            $tabla_turnos[$turno][$dia] .= "<br>" . $nombres;
        }
    }
    ?>
<table id="tabla-turnos">
    <tr>
        <th>TURNO</th>
        <th>HORARIO</th>
        <th>Lunes</th>
        <th>Martes</th>
        <th>Miércoles</th>
        <th>Jueves</th>
        <th>Viernes</th>
        <th>Sábado</th>
        <th>Domingo</th>
    </tr>
    <?php
    foreach (['mañana', 'tarde', 'noche'] as $turno) {
        echo "<tr>";
        echo "<td>" . ucfirst($turno) . "</td>"; 
        echo "<td>";
        switch ($turno) {
            case 'mañana':
                echo "8:00/16:00";
                break;
            case 'tarde':
                echo "16:00/00:00";
                break;
            case 'noche':
                echo "00:00/8:00";
                break;
            default:
                echo "";
                break;
        }
        echo "</td>";
        foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $dia) {
            $dia_utf8 = utf8_encode($dia);
            echo "<td>";
            if (isset($tabla_turnos[$turno][$dia_utf8])) {
                echo $tabla_turnos[$turno][$dia_utf8];
            } else {
                echo "-";  // Puedes cambiar "-" por lo que quieras que aparezca en las celdas vacías
            }
            echo "</td>";
        }
        echo "</tr>";
    }
    ?>
</table>


    <div class="calendario-eventos">
    <div class="campana-container">
        <i class='bx bx-bell campana'></i>
    </div>  
    <h3>NOTIFICACIONES</h3><br>
    <ul>
        <?php
        $nombre_usuario = $_SESSION["usuario"];

        $query_id_usuario = "SELECT ID FROM trabajador WHERE Nombre_Usuario = ?";
        $statement_id_usuario = $conexion->prepare($query_id_usuario);
        $statement_id_usuario->bind_param("s", $nombre_usuario);
        $statement_id_usuario->execute();
        $result_id_usuario = $statement_id_usuario->get_result();

        if ($result_id_usuario->num_rows > 0) {
            $row_id_usuario = $result_id_usuario->fetch_assoc();
            $id_usuario = $row_id_usuario['ID'];
        } else {
            echo "<li>No se encontró el ID del usuario.</li>";
            exit();
        }

        $query_notificaciones = "SELECT Tipo_notificacion, Estado_notificacion, ID_Destinatario, ID_Remitente 
                                 FROM notificaciones 
                                 WHERE (Tipo_notificacion = 'Solicitud de intercambio' AND ID_Destinatario = ? AND Estado_notificacion = 'Pendiente') 
                                    OR (Tipo_notificacion = 'Valoraciones' AND ID_Remitente = ? AND Estado_notificacion = 'Pendiente')";
        $statement_notificaciones = $conexion->prepare($query_notificaciones);
        $statement_notificaciones->bind_param("ii", $id_usuario, $id_usuario);
        $statement_notificaciones->execute();
        $result_notificaciones = $statement_notificaciones->get_result();

        if ($result_notificaciones->num_rows > 0) {
            while ($row_notificacion = $result_notificaciones->fetch_assoc()) {
                $tipo_notificacion = $row_notificacion['Tipo_notificacion'];
                $estado_notificacion = $row_notificacion['Estado_notificacion'];
                echo "<li><strong>$tipo_notificacion</strong> - Estado: $estado_notificacion</li>";
            }
        } else {
            echo "<li>No hay notificaciones pendientes.</li>";
        }
        ?>
    </ul>
</div>
    </div>
        <div class="calendario-eventos">
    <div class="campana-container">
        <i class='bx bx-transfer campana'></i>
    </div> 
    <h3>SOLICITUDES DE INTERCAMBIO ENVIADAS</h3><br>
    <ul>
        <?php
$query_solicitudes = "SELECT tr.Nombre, tr.Apellido, t.Tipo_turno, t.Dia_semana
FROM notificaciones n
JOIN trabajador tr ON n.ID_Destinatario = tr.ID
JOIN turno t ON n.ID_Turno_Solicitado = t.ID
WHERE n.ID_Remitente = ? AND n.Estado_notificacion = 'Pendiente' AND n.Tipo_notificacion = 'Solicitud de intercambio'";
        $statement_solicitudes = $conexion->prepare($query_solicitudes);
        if ($statement_solicitudes === false) {
            die('Error en la preparación de la consulta SQL: ' . $conexion->error);
        }
        $statement_solicitudes->bind_param("i", $id_usuario);
        $statement_solicitudes->execute();
        $result_solicitudes = $statement_solicitudes->get_result();

        if ($result_solicitudes->num_rows > 0) {
            while ($row_solicitudes = $result_solicitudes->fetch_assoc()) {
                $nombre_destinatario = $row_solicitudes['Nombre'];
                $apellido_destinatario = $row_solicitudes['Apellido'];
                $tipo_turno = ucfirst($row_solicitudes['Tipo_turno']);
                $dia_semana = ucfirst(utf8_encode($row_solicitudes['Dia_semana']));
                echo "<li><strong>$nombre_destinatario $apellido_destinatario</strong> - Turno: $tipo_turno, Día: $dia_semana</li>";
            }
        } else {
            echo "<li>No hay solicitudes de intercambio pendientes.</li>";
        }
        ?>
    </ul>
    </div>
    <div class="calendario-eventos">
    <div class="campana-container">
        <i class='bx bx-calendar campana'></i>
    </div> 
    <h3>PRÓXIMOS EVENTOS</h3><br>
    <ul>
        <?php
        $query_eventos = "SELECT DATE_FORMAT(Fecha, '%d/%m/%Y') AS Fecha, Descripcion FROM eventos WHERE Fecha >= CURDATE() ORDER BY YEAR(Fecha) ASC, MONTH(Fecha) ASC, DAY(Fecha) ASC";
        $result_eventos = $conexion->query($query_eventos);

        if ($result_eventos->num_rows > 0) {
            while ($row_eventos = $result_eventos->fetch_assoc()) {
                $fecha_evento = $row_eventos['Fecha'];
                $descripcion_evento = $row_eventos['Descripcion'];
                echo "<li><strong>$fecha_evento:</strong> $descripcion_evento</li>";
            }
        } else {
            echo "<li>No hay eventos próximos.</li>";
        }
        ?>
    </ul>

</section>
</body>
</html>
