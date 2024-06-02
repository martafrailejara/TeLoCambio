<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

if (isset($_SESSION['notificacion'])) {
    $notificacion = $_SESSION['notificacion'];
    unset($_SESSION['notificacion']);
} else {
    $notificacion = ""; 
}

require_once "conexion.php";

$usuario = $_SESSION["usuario"];
$query_usuario = "SELECT ID FROM trabajador WHERE Nombre_Usuario = ?";
$statement_usuario = $conexion->prepare($query_usuario);
$statement_usuario->bind_param("s", $usuario);

if (!$statement_usuario) {
    die("Error al preparar la consulta: " . $conexion->error);
}

if (!$statement_usuario->execute()) {
    die("Error al ejecutar la consulta: " . $statement_usuario->error);
}

$result_usuario = $statement_usuario->get_result();

if ($result_usuario->num_rows == 1) {
    $row_usuario = $result_usuario->fetch_assoc();
    $trabajador_id = $row_usuario['ID'];
} else {
    echo "Error: No se encontró el trabajador actual.";
    exit();
}

$query_notificaciones = "SELECT n.ID, n.Tipo_notificacion, n.ID_Destinatario, t.Tipo_turno, t.Dia_semana 
                         FROM notificaciones n 
                         INNER JOIN turno t ON n.ID_Turno_Solicitado = t.ID 
                         WHERE n.ID_Remitente = ? AND n.Tipo_notificacion = 'Valoraciones' AND n.Estado_notificacion = 'Pendiente'";

$statement_notificaciones = $conexion->prepare($query_notificaciones);
if (!$statement_notificaciones) {
    die("Error al preparar la consulta: " . $conexion->error);
}

$statement_notificaciones->bind_param("i", $trabajador_id);

if (!$statement_notificaciones->execute()) {
    die("Error al ejecutar la consulta: " . $statement_notificaciones->error);
}

$result_notificaciones = $statement_notificaciones->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones</title>
    <link rel="stylesheet" href="/css/estilos.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script>
        function mostrarMensaje(mensaje) {
            alert(mensaje);
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('mensaje')) {
                const mensaje = urlParams.get('mensaje');
                mostrarMensaje(decodeURIComponent(mensaje.replace(/\+/g, ' ')));
            }
        }
    </script>
</head>
<body>
<?php include 'n-bar.php'; ?>
<section class="home-section">
    <div class="encabezado">
        <img src="\img\descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo">TeLoCambio</h1>
        <p class="slogan">¡Cambiando tu experiencia!</p>
    </div>
    <h2>Notificaciones</h2>
    <div class="descripcion-turnos">
        <p>¡Bienvenido/a! A continuación, encontrarás las notificaciones relacionadas con los turnos. Por favor, revisa los detalles.</p>
    </div>
    <br><br>
    <form method="get" action="notificaciones.php">
        <div class="select-container">
            <label for="tipo_notificacion">Seleccionar tipo de notificación:</label>
            <select name="tipo_notificacion" id="tipo_notificacion" onchange="this.form.submit()">
                <option value="Valoraciones" <?php if (isset($_GET['tipo_notificacion']) && $_GET['tipo_notificacion'] == 'Valoraciones') echo 'selected'; ?>>Valoraciones</option>    
                <option value="Solicitud de intercambio" <?php if (isset($_GET['tipo_notificacion']) && $_GET['tipo_notificacion'] == 'Solicitud de intercambio') echo 'selected'; ?>>Solicitud de intercambio</option>
            </select>
        </div>
    </form>
    <div>
        <?php if ($result_notificaciones->num_rows > 0): ?>
        <?php
        while ($row_notificacion = $result_notificaciones->fetch_assoc()) {
            $destinatario_id = $row_notificacion['ID_Destinatario'];
            $query_destinatario = "SELECT Nombre, Apellido FROM trabajador WHERE ID = ?";
            $statement_destinatario = $conexion->prepare($query_destinatario);
            $statement_destinatario->bind_param("i", $destinatario_id);
            $statement_destinatario->execute();
            $result_destinatario = $statement_destinatario->get_result();

            if ($result_destinatario->num_rows == 1) {
                $row_destinatario = $result_destinatario->fetch_assoc();
                echo "<div class='notificacion-box'>";
                echo "<div class='nombre-valoraciones'>";
                echo "<p class='nombret-valoraciones'>" . $row_destinatario['Nombre'] . " " . $row_destinatario['Apellido'] . "</p>";
                echo "<p>Realizó el siguiente turno mediante un intercambio de horarios, es hora de valorarla!    </p>";
                echo "</div><br>";

                echo "<p class='gris-fondo'>" . ucfirst($row_notificacion['Dia_semana']) . "</p><br><br>";

                echo "<p class='info-turno ";
                switch ($row_notificacion['Tipo_turno']) {
                    case "mañana":
                        echo "turno-manana-valoraciones"; 
                        break;
                    case "tarde":
                        echo "turno-tarde-valoraciones";
                        break;
                    case "noche":
                        echo "turno-noche-valoraciones";
                        break;
                }
                echo "'>" . ucfirst($row_notificacion['Tipo_turno']) . "</p>";

                echo "<form action='valorar.php' method='post'>";
                echo "<input type='hidden' name='notificacion_id' value='" . $row_notificacion['ID'] . "'>";
                echo "<button class='n-boton' type='submit'>Valorar</button>";
                echo "</form>";

                echo "</div>"; 
            } else {
                echo "Error: No se encontró el destinatario.";
            }
        }
        ?>
        <?php else: ?>
            <p class="turno-opciones-container">No tienes notificaciones.</p>
        <?php endif; ?>
    </div>
</section>
<script>
    <?php if (!empty($notificacion)) : ?>
        alert("<?php echo $notificacion; ?>");
    <?php endif; ?>
</script>
</body>
</html>
