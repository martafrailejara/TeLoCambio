<?php
// Datos de conexión a la base de datos
$servername = "sql308.infinityfree.com"; 
$username = "if0_36640038"; 
$password = "X84d0dssyd0"; 
$dbname = "if0_36640038_hospital"; 


//123Mart@

$conexion = new mysqli($servername, $username, $password, $dbname);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");

?>
