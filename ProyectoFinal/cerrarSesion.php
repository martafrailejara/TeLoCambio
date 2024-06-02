<?php
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    session_start();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<script>
    var confirmacion = confirm("¿Estás seguro de que deseas cerrar la sesión?");
    if (confirmacion) {
        window.location.href = "cerrarSesion.php?confirm=yes";
    } else {
        var paginaAnterior = document.referrer;
        window.location.href = paginaAnterior;
    }
</script>
