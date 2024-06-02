<?php
session_start();

require_once "../conexion.php";

$query_notificaciones = "SELECT n.ID AS notificacion_id, 
                                tr_remitente.Nombre AS nombre_remitente, 
                                tr_remitente.Apellido AS apellido_remitente,
                                tr_destinatario.Nombre AS nombre_destinatario,
                                tr_destinatario.Apellido AS apellido_destinatario,
                                tr_solicitado.Tipo_turno AS tipo_turno_solicitado,
                                tr_solicitado.Dia_semana AS dia_solicitado,
                                tr_interesado.Tipo_turno AS tipo_turno_interesado,
                                tr_interesado.Dia_semana AS dia_interesado,
                                n.Estado_notificacion
                        FROM notificaciones n
                        INNER JOIN trabajador tr_remitente ON n.ID_Remitente = tr_remitente.ID
                        INNER JOIN trabajador tr_destinatario ON n.ID_Destinatario = tr_destinatario.ID
                        LEFT JOIN turno tr_solicitado ON n.ID_Turno_Solicitado = tr_solicitado.ID
                        LEFT JOIN turno tr_interesado ON n.ID_Turno_Interesado = tr_interesado.ID
                        WHERE n.Tipo_notificacion = 'Solicitud de intercambio' AND n.Estado_notificacion = 'Aceptada'";
$statement_notificaciones = $conexion->prepare($query_notificaciones);
$statement_notificaciones->execute();
$result_notificaciones = $statement_notificaciones->get_result();
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
    <h2>Turnos intercambiados por los trabajadores</h2><br>
    <div class="descripcion-turnos">
        <p>Esta sección muestra los turnos que los trabajadores han aceptado e intercambiado entre sí.</p>
    </div>
    <br><br>
    <div>
        <?php if ($result_notificaciones->num_rows > 0): ?>
            <?php while ($row = $result_notificaciones->fetch_assoc()): ?>
                <div class="form-container">
                    <div class="remite">
                        <span><b><i class='bx bx-user'></i></b> <span class="nombre">De:</span> <?php echo $row['nombre_remitente'] . " " . $row['apellido_remitente']; ?></span><br>
                        <span><b><i class='bx bx-user'></i></b> <span class="nombre">Para:</span> <?php echo $row['nombre_destinatario'] . " " . $row['apellido_destinatario']; ?></span>
                    </div><br>
                    <div class="detalles">
                        <span class="detalle">Han intercambiado la <span class="variable"><?php echo $row['tipo_turno_solicitado']; ?></span></span>
                        <span class="detalle"> del <span class="variable"><?php echo $row['dia_solicitado']; ?></span></span>
                        <span class="detalle"> por la <span class="variable"><?php echo $row['tipo_turno_interesado']; ?></span></span>
                        <span class="detalle"> del <span class="variable"><?php echo $row['dia_interesado']; ?></span></span>
                    </div>
                    <div class="estado">
                        <span class="estado-notificacion">Aceptada</span>
                    </div>
                </div><br>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="form-container">No tienes notificaciones de solicitudes de intercambio aceptadas.</p>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
