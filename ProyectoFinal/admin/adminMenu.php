<?php
require_once "../conexion.php";
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
            <img src="\img\descarga2.png" alt="Logo de TeLoCambio">
            <h1 class="titulo">Menú del Administrador</h1>
            <p class="slogan">¡Bienvenido/a, Administrador/a!</p>
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
    </div>
    <div class="calendario-eventos">
    <div class="campana-container">
        <i class='bx bx-briefcase campana'></i>
    </div>
    <h3>LISTA TRABAJADORES</h3><br><br>
    <ul class="lista-trabajadores-menu"> 
        <?php
        $query_trabajadores = "SELECT ID, Nombre, Apellido, DNI, Correo_electronico, Telefono FROM trabajador";
        $result_trabajadores = $conexion->query($query_trabajadores);

        if ($result_trabajadores->num_rows > 0) {
            while ($row_trabajador = $result_trabajadores->fetch_assoc()) {
                $id_trabajador = $row_trabajador['ID'];
                $nombre = $row_trabajador['Nombre'];
                $apellido = $row_trabajador['Apellido'];
                $dni = $row_trabajador['DNI'];
                $correo = $row_trabajador['Correo_electronico'];
                $telefono = $row_trabajador['Telefono'];
                echo "<li>ID: $id_trabajador - <strong>$nombre $apellido</strong> - DNI: $dni - $correo - Teléfono: $telefono</li>";
            }
        } else {
            echo "<li>No hay trabajadores registrados.</li>";
        }
        ?>
    </ul>
</div>
</section>
</body>
</html>
