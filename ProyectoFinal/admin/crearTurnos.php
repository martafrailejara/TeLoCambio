<?php
require_once "../conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errores = false;
    foreach (['mañana', 'tarde', 'noche'] as $turno) {
        foreach (['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'] as $dia) {
            $nombreCampo = "trabajador_$turno" . "_$dia";
            if (!isset($_POST[$nombreCampo]) || empty($_POST[$nombreCampo])) {
                $errores = true;
                break 2;
            }
        }
    }

    $mensaje = ""; 

    if ($errores) {
        $mensaje = "Por favor, selecciona un trabajador para cada turno antes de guardar.";
    } else {
        $ID_Departamento = $_POST['ID_Departamento'];
    
        $sql_verificar = "SELECT COUNT(*) AS count FROM asignacion_turno WHERE ID_Departamento = '$ID_Departamento'";
        $resultado_verificar = mysqli_query($conexion, $sql_verificar);
        $fila_verificar = mysqli_fetch_assoc($resultado_verificar);
        $existe_asignacion = $fila_verificar['count'] > 0;
    
        if ($existe_asignacion) {
            foreach (['mañana', 'tarde', 'noche'] as $turno) {
                foreach (['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'] as $dia) {
                    $ID_Turno = obtenerIDTurno($turno, $dia);
                    $ID_Trabajador = $_POST["trabajador_$turno" . "_$dia"];
                    $sql_actualizar = "UPDATE asignacion_turno SET ID_Trabajador = '$ID_Trabajador' WHERE ID_Departamento = '$ID_Departamento' AND ID_Turno = '$ID_Turno'";
                    if (!mysqli_query($conexion, $sql_actualizar)) {
                        $mensaje = "Error al actualizar los datos: " . mysqli_error($conexion);
                        exit();
                    }
                }
            }
            $mensaje = "Los datos han sido actualizados correctamente.";
        } else {
            $sql_select = "SELECT ID_Turno, ID_Trabajador FROM asignacion_turno WHERE ID_Departamento = '$ID_Departamento'";
            $resultado_select = mysqli_query($conexion, $sql_select);
            $asignaciones_exist = array();
        
            while ($fila_select = mysqli_fetch_assoc($resultado_select)) {
                $asignaciones_exist[$fila_select['ID_Turno']] = $fila_select['ID_Trabajador'];
            }
        
            foreach (['mañana', 'tarde', 'noche'] as $turno) {
                foreach (['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'] as $dia) {
                    $ID_Turno = obtenerIDTurno($turno, $dia);
                    $ID_Trabajador = $_POST["trabajador_$turno" . "_$dia"];
                    
                    if (isset($asignaciones_exist[$ID_Turno])) {
                        $ID_Trabajador_existente = $asignaciones_exist[$ID_Turno];
                        $sql_update = "UPDATE asignacion_turno SET ID_Trabajador = '$ID_Trabajador' WHERE ID_Departamento = '$ID_Departamento' AND ID_Turno = '$ID_Turno'";
                        if (!mysqli_query($conexion, $sql_update)) {
                            $mensaje = "Error al actualizar los datos: " . mysqli_error($conexion);
                            exit();
                        }
                    } else {
                        $sql_insertar = "INSERT INTO asignacion_turno (ID_Departamento, ID_Turno, ID_Trabajador, Publicado_libre) VALUES ('$ID_Departamento', '$ID_Turno', '$ID_Trabajador', 0)";
                        if (!mysqli_query($conexion, $sql_insertar)) {
                            $mensaje = "Error al guardar los datos: " . mysqli_error($conexion);
                            exit();
                        }
                    }
                }
            }
            $mensaje = "Los datos han sido guardados correctamente.";
        }
    }
}
function obtenerIDTurno($turno, $dia) {
    switch ($turno) {
        case 'mañana':
            $hora_inicio = '08:00:00';
            break;
        case 'tarde':
            $hora_inicio = '16:00:00';
            break;
        case 'noche':
            $hora_inicio = '00:00:00';
            break;
        default:
            $hora_inicio = '00:00:00';
            break;
    }

    switch ($dia) {
        case 'lunes':
            $num_dia = 1;
            break;
        case 'martes':
            $num_dia = 2;
            break;
        case 'miércoles':
            $num_dia = 3;
            break;
        case 'jueves':
            $num_dia = 4;
            break;
        case 'viernes':
            $num_dia = 5;
            break;
        case 'sábado':
            $num_dia = 6;
            break;
        case 'domingo':
            $num_dia = 7;
            break;
        default:
            $num_dia = 0;
            break;
    }

    return (($num_dia - 1) * 3) + ($turno == 'mañana' ? 1 : ($turno == 'tarde' ? 2 : 3));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TeLoCambio Administrador</title>
    <link rel="stylesheet" href="/css/estilosAdministrador.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<?php include 'n-barAdmin.php'; ?>
<section class="home-section">
    <div class="encabezado">
        <img src="/img/descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo alta">Menú del Administrador</h1>
    </div>
    <h2>Sistema de gestión de creación de turnos por departamento</h2><br>
    <div class="descripcion-turnos">
        <p>Utiliza el siguiente formulario para crear turnos para los diferentes departamentos. Puedes seleccionar un departamento y luego pulsar en el botón para crear los turnos correspondientes.</p>
    </div><br><br>
<?php
    if (isset($_POST['ID_Departamento'])) {

        $ID_Departamento = $_POST['ID_Departamento'];
        $sql_departamento = "SELECT Nombre FROM departamentos WHERE ID = '$ID_Departamento'";
        $resultado_departamento = mysqli_query($conexion, $sql_departamento);
        if ($fila_departamento = mysqli_fetch_assoc($resultado_departamento)) {
            echo "<h2 class='form-container2'>{$fila_departamento['Nombre']} (ID: $ID_Departamento)</h2><br>";
        }
    }
    ?>

    <form action="" method="post">
        <?php if (isset($_POST['ID_Departamento'])): ?>
            <input type="hidden" name="ID_Departamento" value="<?php echo $ID_Departamento; ?>">
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
                    foreach (['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'] as $dia) {
                        echo "<td>";
                        echo "<select name='trabajador_$turno" . "_$dia'>";
                        echo "<option value=''>Seleccionar trabajador</option>";
                        $sql = "SELECT ID, Nombre, Apellido FROM trabajador WHERE Numero_Departamento = $ID_Departamento";
                        $resultado = mysqli_query($conexion, $sql);
                        while($fila = mysqli_fetch_assoc($resultado)) {
                            echo "<option value='" . $fila['ID'] . "'>" . $fila['Nombre'] . " " . $fila['Apellido'] . "</option>";
                        }
                        echo "</select>";
                        echo "</td>";
                    }                    
                    echo "</tr>";
                }
                ?>
            </table>
            <br>
            <div class="container-tur">
                <?php echo $mensaje; ?>
            </div>
            <br>
            <div class="botones-container-tur" style="text-align: center;">
                <input type="submit" value="Guardar" class="n-boton-tur">
            </div>
            <?php else: ?>
            <div class="container-tur">
                <label for="departamento" style="font-weight: bold;">Selecciona un Departamento:</label>
                <select name="ID_Departamento" id="departamento" style="margin-bottom: 10px; width: 200px; padding: 5px;">
                    <?php
                    require_once "../conexion.php"; 
                    session_start();
                    $sql = "SELECT ID, Nombre FROM departamentos";
                    $resultado = mysqli_query($conexion, $sql);
                    if (!$resultado) {
                        die("Error al obtener los departamentos: " . mysqli_error($conexion));
                    }

                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        echo "<option value='" . $fila["ID"] . "'>" . $fila["Nombre"] . "</option>";
                    }
                    mysqli_close($conexion);
                    ?>
                </select>
                <div class="botones-container-tur" style="text-align: center;">
                    <input type="submit" value="Crear turnos para ese departamento" class="n-boton-tur" style="width: 250px; padding: 10px;">
                </div>
            </div>

        <?php endif; ?>
    </form>
</section>
</body>
</html>
