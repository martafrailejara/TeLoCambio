<?php
session_start();

if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
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
    exit("Error: No se pudo obtener el ID del trabajador.");
}

$notificacion_id = $_POST['notificacion_id'];
$query_notificacion = "SELECT * FROM notificaciones WHERE ID = ?";
$statement_notificacion = $conexion->prepare($query_notificacion);
$statement_notificacion->bind_param("i", $notificacion_id);
$statement_notificacion->execute();
$result_notificacion = $statement_notificacion->get_result();

if ($result_notificacion->num_rows == 1) {
    $row_notificacion = $result_notificacion->fetch_assoc();
} else {
    exit("Error: No se pudo obtener la notificación.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valorar Servicio</title>
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
    <h2>Valorar Servicio de intercambio</h2><br><br>
    <div class="turno-opciones-container">
        <p>Por favor, valora el servicio del trabajador del 1 al 5:</p><br>
        <form action="procesar_valoracion.php" method="post" id="valoracionForm">
            <input type="hidden" name="notificacion_id" value="<?php echo $row_notificacion['ID']; ?>">
            <div class="estrellas">
                <input type="radio" id="estrella1" name="valoracion" value="1">
                <label for="estrella1"><i class='bx bxs-star'></i></label>
                <input type="radio" id="estrella2" name="valoracion" value="2">
                <label for="estrella2"><i class='bx bxs-star'></i></label>
                <input type="radio" id="estrella3" name="valoracion" value="3">
                <label for="estrella3"><i class='bx bxs-star'></i></label>
                <input type="radio" id="estrella4" name="valoracion" value="4">
                <label for="estrella4"><i class='bx bxs-star'></i></label>
                <input type="radio" id="estrella5" name="valoracion" value="5">
                <label for="estrella5"><i class='bx bxs-star'></i></label>
            </div>
            <div class="comentarios">
                <label for="comentario">Comentario (opcional):</label>
                <textarea id="comentario" name="comentario"></textarea>
            </div>
            <button class="n-boton" type="submit">Enviar Valoración</button>
        </form>
    </div>
</section>
<script>
    const estrellas = document.querySelectorAll('.estrellas input[type="radio"]');
    const form = document.getElementById('valoracionForm');

    form.addEventListener('submit', function(event) {
        let checked = false;
        estrellas.forEach(estrella => {
            if (estrella.checked) {
                checked = true;
            }
        });

        if (!checked) {
            alert("Por favor, selecciona una valoración antes de enviar.");
            event.preventDefault();
        }
    });

    estrellas.forEach((estrella, index) => {
        estrella.addEventListener('change', () => {
            estrellas.forEach(otraEstrella => {
                const etiquetaOtraEstrella = otraEstrella.nextElementSibling;
                etiquetaOtraEstrella.querySelector('i').style.color = '#ccc';
            });

            for (let i = 0; i <= index; i++) {
                const etiquetaEstrella = estrellas[i].nextElementSibling;
                etiquetaEstrella.querySelector('i').style.color = 'yellow';
            }
        });
    });
</script>
</body>
</html>
