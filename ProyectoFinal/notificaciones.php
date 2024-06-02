<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: menuAdmin.php");
    exit();
}

require_once "conexion.php";

$usuario = $_SESSION["usuario"];
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

$tipo_notificacion = isset($_GET['tipo_notificacion']) ? $_GET['tipo_notificacion'] : 'Solicitud de intercambio';

$query_notificaciones = "SELECT n.ID AS notificacion_id, n.Tipo_notificacion, n.Estado_notificacion, 
                        tr.Tipo_turno AS tipo_turno_remitente, tr.Dia_semana AS dia_remitente, 
                        td.Tipo_turno AS tipo_turno_destinatario, td.Dia_semana AS dia_destinatario, 
                        tru.Nombre AS nombre_remitente, tru.Apellido AS apellido_remitente
                        FROM notificaciones n
                        LEFT JOIN trabajador tru ON n.ID_Remitente = tru.ID
                        LEFT JOIN turno tr ON n.ID_Turno_Solicitado = tr.ID
                        LEFT JOIN trabajador trd ON n.ID_Destinatario = trd.ID
                        LEFT JOIN turno td ON n.ID_Turno_Interesado = td.ID
                        WHERE n.ID_Destinatario = ? AND n.Tipo_notificacion = ?
                        ORDER BY FIELD(n.Estado_notificacion, 'Pendiente', 'Aceptada', 'Rechazada')";
$statement_notificaciones = $conexion->prepare($query_notificaciones);
$statement_notificaciones->bind_param("is", $trabajador_id, $tipo_notificacion);
$statement_notificaciones->execute();
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
                <option value="Solicitud de intercambio" <?php if (isset($_GET['tipo_notificacion']) && $_GET['tipo_notificacion'] == 'Solicitud de intercambio') echo 'selected'; ?>>Solicitud de intercambio</option>
                <option value="Valoraciones" <?php if (isset($_GET['tipo_notificacion']) && $_GET['tipo_notificacion'] == 'Valoraciones') echo 'selected'; ?>>Valoraciones</option>
            </select>
        </div>
    </form>
    <div class="notificaciones-container">
        <?php if ($result_notificaciones->num_rows > 0): ?>
            <?php while ($row = $result_notificaciones->fetch_assoc()): ?>
                <?php
                $notificacion_id = $row['notificacion_id'];
                $tipo_notificacion = $row['Tipo_notificacion'];
                $tipo_turno_remitente = $row['tipo_turno_remitente'];
                $dia_remitente = $row['dia_remitente'];
                $tipo_turno_destinatario = $row['tipo_turno_destinatario'];
                $dia_destinatario = $row['dia_destinatario'];
                $nombre_remitente = $row['nombre_remitente'];
                $apellido_remitente = $row['apellido_remitente'];
                $estado_notificacion = $row['Estado_notificacion'];
                ?>
                <div class="notificacion">
                    <div class="remite">
                        <span class="nombre"><?php echo "$nombre_remitente $apellido_remitente  "; ?>   |||  </span>
                        <span class="tipo-notificacion"><?php echo $tipo_notificacion; ?></span>
                    </div><br>
                    <div class="detalles">
                    <span class="detalle">Te quiere intercambiar la <span class="variable"><?php echo $tipo_turno_remitente; ?></span></span>
                        <span class="detalle"> del <span class="variable"><?php echo $dia_remitente; ?></span></span>
                        <span class="detalle"> por tu turno de la <span class="variable"><?php echo $tipo_turno_destinatario; ?></span></span>
                        <span class="detalle"> del <span class="variable"><?php echo $dia_destinatario; ?></span></span>
                    </div>
                    <div class="estado">
                        <span class="estado-notificacion"><?php echo $estado_notificacion; ?></span>
                        <?php if ($estado_notificacion == 'Pendiente'): ?>
                            <form method="post" action="procesar_notificacion.php" class="accion-form">
                                <input type="hidden" name="notificacion_id" value="<?php echo $notificacion_id; ?>">
                                <button class="n-boton" type="submit" name="accion" value="aceptar">Aceptar</button>
                                <button class="n-boton" type="submit" name="accion" value="rechazar">Rechazar</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="turno-opciones-container">No tienes notificaciones.</p>
        <?php endif; ?>
    </div>
</section>
</body>
<script src="\scripts\script.js"></script>
<script>
document.getElementById('tipo_notificacion').addEventListener('change', function() {
    if (this.value === 'Valoraciones') {
        window.location.href = 'valoraciones.php';
    } else {
        document.getElementById('notificacionesForm').submit();
    }
});
</script>
</html>