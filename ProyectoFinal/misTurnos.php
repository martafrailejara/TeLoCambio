<?php
    session_start();

    if (!isset($_SESSION["usuario"])) {
        header("Location: index.php");
        exit();
    }

    require_once "conexion.php";

    $usuario = $_SESSION["usuario"];

    $query = "SELECT ID, Nombre, Apellido FROM trabajador WHERE Nombre_Usuario = ?";
    $statement = $conexion->prepare($query);
    $statement->bind_param("s", $usuario);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $trabajador_id = $row['ID'];
        $nombre = $row['Nombre'];
        $apellido = $row['Apellido'];
    }

    $query_turnos = "SELECT t.ID, t.Tipo_turno, t.Dia_semana
                    FROM turno t
                    LEFT JOIN asignacion_turno at ON t.ID = at.ID_Turno
                    WHERE at.ID_Trabajador = ?";
    $statement_turnos = $conexion->prepare($query_turnos);
    $statement_turnos->bind_param("i", $trabajador_id);
    $statement_turnos->execute();
    $result_turnos = $statement_turnos->get_result();

    $mis_turnos = array();

    while ($row_turnos = $result_turnos->fetch_assoc()) {
        $turno_id = $row_turnos['ID'];
        $turno = $row_turnos['Tipo_turno'];
        $dia = $row_turnos['Dia_semana'];
        $mis_turnos[$turno][$dia] = $turno_id; 
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis turnos</title>
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
    <h2>Estos son tus turnos de esta semana</h2><br>
    <div class="descripcion-turnos">
      <p>A continuación, encontrarás tus turnos programados para esta semana. Por favor, revisa la tabla para ver los detalles.</p>
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
            $horarios = array(
                "mañana" => "8:00/16:00",
                "tarde" => "16:00/00:00",
                "noche" => "00:00/8:00"
            );

            foreach (['mañana', 'tarde', 'noche'] as $turno) {
                echo "<tr>";
                echo "<td>" . ucfirst($turno) . "</td>";
                echo "<td>" . $horarios[$turno] . "</td>";
                
                foreach (['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $dia) {
                    echo "<td>";
                    if (isset($mis_turnos[$turno][$dia])) {
                        $turno_id = $mis_turnos[$turno][$dia];
                        echo "<a href='opciones.php?turno_id=$turno_id&trabajador_id=$trabajador_id'>$nombre $apellido</a>";
                    }
                    echo "</td>";
                }

                echo "</tr>";
            }
        ?>
    </table>
</section>
</body>
</html>