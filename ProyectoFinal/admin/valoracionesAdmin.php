<?php 
require_once "../conexion.php";

$busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';

$query_valoraciones = "SELECT v.Puntuacion, v.Comentario, tv.Nombre AS Nombre_Valorador, tv.Apellido AS Apellido_Valorador, tt.Nombre AS Nombre_Trabajador, tt.Apellido AS Apellido_Trabajador
                       FROM valoraciones v
                       INNER JOIN trabajador tv ON v.ID_Valorador = tv.ID
                       INNER JOIN trabajador tt ON v.ID_Trabajador_Valorado = tt.ID
                       WHERE tv.Nombre LIKE ? OR tv.Apellido LIKE ? OR tt.Nombre LIKE ? OR tt.Apellido LIKE ?
                       ORDER BY tv.Nombre, tv.Apellido, tt.Nombre, tt.Apellido"; // Ordenar por nombre y apellido para mostrar los resultados de manera ordenada

$statement_valoraciones = $conexion->prepare($query_valoraciones);

if (!$statement_valoraciones) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$search = "%$busqueda%";
$statement_valoraciones->bind_param("ssss", $search, $search, $search, $search);

$statement_valoraciones->execute();
$result_valoraciones = $statement_valoraciones->get_result();

if (!$result_valoraciones) {
    die("Error al ejecutar la consulta: " . $conexion->error);
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
    <script>
        function confirmarEliminacion() {
            return confirm("¿Estás seguro de que deseas eliminar este empleado?");
        }

        function validarBusqueda() {
            var busqueda = document.getElementById("busqueda").value;
            if (busqueda.length < 3) {
                alert("Por favor, ingrese al menos 3 caracteres para buscar.");
                return false;
            }
            return true;
        }

        function reiniciarBusqueda() {
            document.getElementById("busqueda").value = '';
            window.location.href = window.location.pathname;
        }
    </script>
</head>
<body>
<?php include 'n-barAdmin.php'; ?> 
<section class="home-section">
    <div class="encabezado">
        <img src="/img/descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo alta">Menú del Administrador</h1>
    </div>
    <h2>Calificaciones</h2><br>
    <div class="descripcion-turnos">
      <p>Estas son las valoraciones de los turnos que han intercambiado los trabajadores</p>
      </div><br><br>
      <div class="search-container-wrapper">
    <form method="get" action="" onsubmit="return validarBusqueda();" class="search-container">
        <input type="text" id="busqueda" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>" placeholder="Buscar...">
        <button type="submit">
            <i class='bx bx-search'></i>
        </button>
        <button type="button" onclick="reiniciarBusqueda()">
            <i class='bx bx-x'></i> 
        </button>
    </form>
</div>
<br>
    <div>
        <?php
        if ($result_valoraciones->num_rows > 0) {
            // Mostrar las valoraciones y turnos
            while ($row_valoracion = $result_valoraciones->fetch_assoc()) {
                $puntuacion = $row_valoracion['Puntuacion'];
                $comentario = $row_valoracion['Comentario'];
                $nombre_Valorador = $row_valoracion['Nombre_Valorador'];
                $apellido_Valorador = $row_valoracion['Apellido_Valorador'];
                $nombre_Trabajador = $row_valoracion['Nombre_Trabajador'];
                $apellido_Trabajador = $row_valoracion['Apellido_Trabajador'];

                echo "<div class='turno-calificaciones'>";
                    echo "<p class='nombre-calificaciones'>Valorado por: <span class='valorador-nombre'>$nombre_Valorador $apellido_Valorador</span></p>";
                    echo "<p class='nombre-calificaciones'>Valorado a: <span class='valorador-nombre'>$nombre_Trabajador $apellido_Trabajador</span></p>";
                    echo "<p><br>";
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= $puntuacion) {
                            echo "<i class='bx bxs-star estrellas-calificaciones amarillo'></i>";
                        } else {
                            echo "<i class='bx bx-star estrellas-calificaciones'></i>";
                        }
                    }
                    echo "</p>";
                    if (!empty($comentario)) {
                        echo "<p> <br>Comentario: <span class='comentario-calificaciones'>$comentario</span></p>";
                    }
                echo "</div><br>";
            }
        } else {
            echo "<p class='turno-calificaciones'>No se han encontrado resultados para la búsqueda.</p>";
        }
        ?>
    </div>
</section>
</body>
</html>
