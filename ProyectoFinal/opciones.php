<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit();
}

require_once "conexion.php";

if (!isset($_GET["turno_id"]) || empty($_GET["turno_id"])) {
    header("Location: misTurnos.php");
    exit();
}

$id_turno = $_GET["turno_id"];
$trabajador_id = $_GET["trabajador_id"];

$query_turno = "SELECT Tipo_turno, Dia_semana FROM turno WHERE ID = ?";
$statement_turno = $conexion->prepare($query_turno);
$statement_turno->bind_param("i", $id_turno);
$statement_turno->execute();

$result_turno = $statement_turno->get_result();

if ($result_turno->num_rows == 1) {
    $row_turno = $result_turno->fetch_assoc();
    $tipo_turno = $row_turno["Tipo_turno"];
    $dia_semana = $row_turno["Dia_semana"];
} else {
    header("Location: error.php");
    exit();
}

function obtener_clase_color($tipo_turno) {
    switch ($tipo_turno) {
        case 'mañana':
            return 'turno-mañana';
        case 'tarde':
            return 'turno-tarde';
        case 'noche':
            return 'turno-noche';
        default:
            return 'turno-normal';
    }
}

function obtener_tipo_turno($tipo_turno) {
    switch ($tipo_turno) {
        case 'mañana':
            return 'Mañana';
        case 'tarde':
            return 'Tarde';
        case 'noche':
            return 'Noche';
        default:
            return 'Normal';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opciones para el Turno</title>
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
    <div class="turno-opciones-container">
        <h2>Opciones para el Turno</h2><br>
        <div class="descripcion-turnos">
            <p>En esta sección, puedes gestionar el turno actual. Puedes elegir entre publicarlo como disponible para otros trabajadores o intercambiarlo con otro turno disponible.</p>
        </div>
        <div class="turno-info">
            <br>
            <p><strong>Día de la Semana</strong></p>
            <p class="turno-dia-semana">
                <span><?php echo ucfirst($dia_semana); ?></span>
            </p>
            <p><strong>Tipo de Turno</strong></p>
            <p class="<?php echo obtener_clase_color($tipo_turno); ?>">
                <span><?php echo obtener_tipo_turno($tipo_turno); ?></span>
            </p>
        </div>
        <br>
    </div>
    <div class="botones-container">
        <button class="n-boton"><a href="publicarLibre.php?turno_id=<?php echo $id_turno; ?>&trabajador_id=<?php echo $trabajador_id; ?>">Publicar Turno como Libre</a></button>
        <button class="n-boton"><a href="intercambiar.php?turno_id=<?php echo $id_turno; ?>&trabajador_id=<?php echo $trabajador_id; ?>">Intercambiar Turno</a></button>
    </div>
</section>
</body>
</html>
