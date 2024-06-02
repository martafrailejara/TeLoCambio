<?php
require_once "conexion.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $contrasena = $_POST["contrasena"];
    $tipo_usuario = $_POST["tipo_usuario"];

    $query = "SELECT * FROM trabajador WHERE Nombre_Usuario = ?";

    $statement = $conexion->prepare($query);

    if ($statement === false) {
        die('Error en la consulta SQL: ' . $conexion->error);
    }

    $statement->bind_param("s", $usuario);
    $statement->execute();
    $result = $statement->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($contrasena, $row['Contrasena'])) {
            $_SESSION["usuario"] = $usuario;
            if ($tipo_usuario == "admin" && $row['Es_administrador'] == 1) {
                header("Location: /admin/adminMenu.php");
                exit();
            } elseif ($tipo_usuario == "trabajador" && $row['Es_administrador'] == 0) {
                header("Location: menu.php");
                exit();
            } else {
                $error_message = "Tipo de usuario incorrecto.";
                echo "<script>alert('$error_message');</script>";
            }
        } else {
            $error_message = "Usuario o contraseña incorrectos. Por favor, inténtelo de nuevo.";
            echo "<script>alert('$error_message');</script>";
        }
    } else {
        $error_message = "Usuario o contraseña incorrectos. Por favor, inténtelo de nuevo.";
        echo "<script>alert('$error_message');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TE LO CAMBIO</title>
    <link rel="stylesheet" href="\css\estilosInicioSesion.css">
    <link rel="icon" href="/img/descarga.ico" type="image/x-icon>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form class="formulario" method="post" action="index.php">
                <h1>Acceder como Administrador</h1><br>
                <span>o usa tu cuenta de trabajador</span>
                <input type="hidden" name="tipo_usuario" value="admin">
                <input type="text" id="usuario" name="usuario" pattern="^\S+$" class="campo" required placeholder="Usuario">
                <input type="password" id="contrasena" name="contrasena" class="campo" required placeholder="Contraseña"><br>
                <button type="submit" class="boton">Iniciar Sesión</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form class="formulario" method="post" action="index.php">
                <h1>Acceder como Trabajador</h1><br>
                <span>o usa tu cuenta de administrador</span>
                <input type="hidden" name="tipo_usuario" value="trabajador">
                <input type="text" id="usuario" name="usuario" pattern="^\S+$" class="campo" required placeholder="Usuario">
                <input type="password" id="contrasena" name="contrasena" class="campo" required placeholder="Contraseña"><br>
                <button type="submit" class="boton">Iniciar Sesión</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                <img src="\img\descarga.png" class="imagen"><br>
                    <h1>Te Lo Cambio</h1>
                    <p>Ingrese sus datos personales para iniciar sesión como trabajador</p>
                    <button class="ghost" id="signIn">Trabajador</button>
                </div>
                <div class="overlay-panel overlay-right">
                <img src="\img\descarga.png" class="imagen"><br>
                    <h1>Te Lo Cambio</h1>
                    <p>Ingrese sus datos personales para iniciar sesión como administrador</p>
                    <button class="ghost" id="signUp">Administrador</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const signUpButton = document.getElementById('signUp');
            const signInButton = document.getElementById('signIn');
            const container = document.getElementById('container');

            signUpButton.addEventListener('click', () => {
                container.classList.add("right-panel-active");
            });

            signInButton.addEventListener('click', () => {
                container.classList.remove("right-panel-active");
            });
        });
    </script>
</body>
</html>
