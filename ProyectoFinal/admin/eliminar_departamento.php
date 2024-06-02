<?php
require_once "../conexion.php"; 
session_start();

$conexion = mysqli_connect($servername, $username, $password, $dbname);

if (!$conexion) {
    die("La conexión ha fallado: " . mysqli_connect_error());
}

if (isset($_POST['eliminar'])) {
    $id = $_POST['id'];
    eliminarDepartamento($conexion, $id);
}

function eliminarDepartamento($conexion, $id) {
    $sql = "DELETE FROM departamentos WHERE ID = ?";
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: departamentos.php");
    exit;
}
?>
