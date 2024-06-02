<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

$usuario = $_SESSION["usuario"];

require_once "conexion.php";

$query_usuario = "SELECT ID FROM trabajador WHERE Nombre_Usuario = ?";
$statement_usuario = $conexion->prepare($query_usuario);
$statement_usuario->bind_param("s", $usuario);
$statement_usuario->execute();
$result_usuario = $statement_usuario->get_result();

if ($result_usuario->num_rows == 1) {
    $row_usuario = $result_usuario->fetch_assoc();
    $trabajador_id = $row_usuario['ID'];
} else {
    echo "Error: No se encontró el trabajador actual.";
    exit();
}

$query = "SELECT t.ID AS turno_id, t.Tipo_turno, t.Dia_semana, 
                 tr.Nombre AS nombre_trabajador, tr.Apellido AS apellido_trabajador, tr.ID AS id_trabajador,
                 at.Publicado_libre
          FROM turno t
          LEFT JOIN asignacion_turno at ON t.ID = at.ID_Turno
          LEFT JOIN trabajador tr ON at.ID_Trabajador = tr.ID
          WHERE tr.ID != ?
          ORDER BY FIELD(t.Tipo_turno, 'mañana', 'tarde', 'noche'), FIELD(t.Dia_semana, 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo')";

$statement = $conexion->prepare($query);
$statement->bind_param("i", $trabajador_id);
$statement->execute();
$result = $statement->get_result();
$tabla_turnos = array();

while ($row = $result->fetch_assoc()) {
    $turno = $row['Tipo_turno'];
    $dia = $row['Dia_semana'];
    $nombreTrabajador = $row['nombre_trabajador'];
    $apellidoTrabajador = $row['apellido_trabajador'];
    $idTrabajador = $row['id_trabajador'];
    $turno_id = $row['turno_id'];
    $publicado_libre = $row['Publicado_libre'];

    $nombreCompleto = $nombreTrabajador . ' ' . $apellidoTrabajador;

    if (!isset($tabla_turnos[$turno])) {
        $tabla_turnos[$turno] = array();
    }

    if (!isset($tabla_turnos[$turno][$dia])) {
        $tabla_turnos[$turno][$dia] = array();
    }

    $tabla_turnos[$turno][$dia][] = array('nombre_completo' => $nombreCompleto, 'id' => $idTrabajador, 'turno_id' => $turno_id, 'publicado_libre' => $publicado_libre);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intercambiar Turno</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script>
        function mostrarDetalles(turno_id, turno, dia, horario, nombreCompleto, trabajadorId) {
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            turno = capitalizeFirstLetter(turno);
            dia = capitalizeFirstLetter(dia);

            var urlParams = new URLSearchParams(window.location.search);
            var turno_id_url = urlParams.get('turno_id');
            var trabajador_id_url = urlParams.get('trabajador_id');

            var detalles = "<div>";
            detalles += "<p>Trabajador al que vas a intercambiar el turno: <strong>" + nombreCompleto + "</strong></p>";
            detalles += "<br><p class='dia-detalle'><span class='dia'>" + dia + "</span></p>";
            detalles += "<p class='turno-detalle'><br><span class='turno-" + turno.toLowerCase() + "'>" + turno + "</span></p>";
            detalles += '<form id="form-notificacion" method="post" action="enviar_notificacion.php" onsubmit="return confirmarEnvioNotificacion();">';
            detalles += '<input type="hidden" name="trabajador_id" value="' + trabajadorId + '">';
            detalles += '<input type="hidden" name="turno_id" value="' + turno_id + '">';

            detalles += '<input type="hidden" name="turno_id_url" value="' + turno_id_url + '">';
            detalles += '<input type="hidden" name="trabajador_id_url" value="' + trabajador_id_url + '">';

            detalles += '<button class="n-boton" type="submit">Enviar Notificación</button>';
            detalles += '</form>';
            detalles += "</div>";

            document.getElementById("detalles-turno").innerHTML = detalles;
        }

        function confirmarEnvioNotificacion() {
            return confirm("¿Estás seguro de enviar la notificación? Esta acción no se puede deshacer.");
        }
    </script>
    <style>
        #tabla-turnos td:hover {
            background-color: #D0D0D0;
            cursor: pointer;
        }

        .selected-turno {
            background-color: red;
        }

        .turno-mañana {
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block; 
            font-size: 20px;
            background-color: #FFF86E;
            color: black;
        }

        .turno-tarde {
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block; 
            font-size: 20px;
            background-color: #FFAE6E;
        }

        .turno-noche {
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block; 
            font-size: 20px;
            background-color: #6654A5;
            color: white;
        }

        .dia {
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block; 
            font-size: 20px;
            background-color: #757575;
            color: white;
        }

        .libre {
            color: green;
            font-weight: bold;
        }
        th, td {
            text-transform: capitalize;
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
        <p>¡Bienvenido/a! A continuación, encontrarás los turnos programados para esta semana. Por favor, revisa la tabla para ver los detalles.<br>Haz click en el nombre del trabajador para ver la información</p>
    </div><br>
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
                echo "<td data-turno-id='$turno'>" . ucfirst($turno) . "</td>";
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
                    echo "<td>";
                    if (isset($tabla_turnos[$turno][$dia])) {
                        foreach ($tabla_turnos[$turno][$dia] as $trabajador) {
                            $nombreCompleto = $trabajador['nombre_completo'];
                            $trabajadorId = $trabajador['id'];
                            $turno_id = $trabajador['turno_id'];
                            $libre = $trabajador['publicado_libre'] == 1 ? 'libre' : '';
                            echo "<span class='nombre-trabajador $libre' onclick=\"mostrarDetalles('$turno_id', '$turno', '$dia', '".($turno == 'mañana' ? '8:00 - 16:00' : ($turno == 'tarde' ? '16:00 - 00:00' : '00:00 - 8:00'))."', '$nombreCompleto', '$trabajadorId')\">" . $nombreCompleto . "</span><br>";
                        }
                    }
                    echo "</td>";
                }
                echo "</tr>";
            }
        ?>
    </table><br><br>
    <div id="detalles-turno" class="turno-opciones-container">
        <p>Selecciona un trabajador y aquí se cargará la información</p>
    </div>
</section>
</body>
</html>
