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
    if (!$statement_turno) {
        die("Error al preparar la consulta: " . $conexion->error);
    }
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $query_actualizar = "UPDATE asignacion_turno SET Publicado_libre = 1 WHERE ID_Turno = ? AND ID_Trabajador = ?";
    $statement_actualizar = $conexion->prepare($query_actualizar);
    $statement_actualizar->bind_param("ii", $id_turno, $trabajador_id);
    $statement_actualizar->execute();

    header("Location: misTurnos.php");
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
    <title>Publicar Turno Como Libre</title>
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
        <h2>Publicar Turno Como Libre</h2><br>
        <div class="descripcion-turnos">
        <p>En esta sección, puedes poner tu turno a disposición de otros trabajadores. Si no puedes cubrir tu turno, puedes publicarlo como libre para que otro compañero lo tome.</p>
        </div><br>
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
        </div>
        <div class="botones-container">
            <form method="post">
                <button type="submit" class="n-boton" name="confirmar">Confirmar Publicación</button>
            </form>
        </div>
        <br>
    </div>
</section>
</body>
</html>