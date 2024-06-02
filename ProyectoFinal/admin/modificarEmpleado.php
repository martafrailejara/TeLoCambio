<?php
class validDni
{
    public static function check($value)
    {
        $pattern = "/^[XYZ]?\d{5,8}[A-Z]$/";
        $dni = strtoupper($value);
        if (preg_match($pattern, $dni))
        {
            $number = substr($dni, 0, -1);
            $number = str_replace(['X', 'Y', 'Z'], [0, 1, 2], $number);
            $dniLetter = substr($dni, -1, 1);
            $start = $number % 23;
            $letter = 'TRWAGMYFPDXBNJZSQVHLCKET';
            $expectedLetter = $letter[$start];
            return $expectedLetter === $dniLetter;
        }
        return false;
    }
}

require_once "../conexion.php";
session_start();

$errors = [];
$nombre = $apellido = $dni = $correo = $telefono = $direccion = $nombre_usuario = "";
$es_administrador = 0;
$numero_departamento = null;

$departamentos = [];
$sql = "SELECT ID, Nombre FROM departamentos";
$result = mysqli_query($conexion, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $departamentos[] = $row;
}

$id = $_GET['id'];
$sql = "SELECT * FROM trabajador WHERE ID = ?";
$stmt = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

$empleado = mysqli_fetch_assoc($resultado);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $dni = trim($_POST['dni']);
    $correo = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $es_administrador = isset($_POST['es_administrador']) ? 1 : 0;
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $numero_departamento = $_POST['numero_departamento'];

    // Validación de DNI
    if (!validDni::check($dni)) {
        $errors['dni'] = "<br>*El DNI no es válido.";
    }

    // Verificaciones únicas
    if (empty($errors)) {
        // DNI
        $stmt = $conexion->prepare("SELECT ID FROM trabajador WHERE DNI = ? AND ID != ?");
        $stmt->bind_param("si", $dni, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['dni'] = "<br>*El DNI ya está registrado.";
        }
        $stmt->close();

        // Correo electrónico
        $stmt = $conexion->prepare("SELECT ID FROM trabajador WHERE Correo_electronico = ? AND ID != ?");
        $stmt->bind_param("si", $correo, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['correo'] = "<br>*El correo electrónico ya está registrado.";
        }
        $stmt->close();

        // Nombre de usuario
        $stmt = $conexion->prepare("SELECT ID FROM trabajador WHERE Nombre_Usuario = ? AND ID != ?");
        $stmt->bind_param("si", $nombre_usuario, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors['nombre_usuario'] = "<br>*El nombre de usuario ya está en uso.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        if (!empty($contrasena)) {
            // Si la contraseña no está vacía, actualizarla
            $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
            $sql = "UPDATE trabajador SET Nombre = ?, Apellido = ?, DNI = ?, Correo_electronico = ?, Contrasena = ?, Es_administrador = ?, Telefono = ?, Direccion = ?, Nombre_Usuario = ?, Numero_Departamento = ? WHERE ID = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "sssssiisssi", $nombre, $apellido, $dni, $correo, $hashed_password, $es_administrador, $telefono, $direccion, $nombre_usuario, $numero_departamento, $id);
        } else {
            // Si la contraseña está vacía, no actualizarla
            $sql = "UPDATE trabajador SET Nombre = ?, Apellido = ?, DNI = ?, Correo_electronico = ?, Es_administrador = ?, Telefono = ?, Direccion = ?, Nombre_Usuario = ?, Numero_Departamento = ? WHERE ID = ?";
            $stmt = mysqli_prepare($conexion, $sql);
            mysqli_stmt_bind_param($stmt, "sssssiissi", $nombre, $apellido, $dni, $correo, $es_administrador, $telefono, $direccion, $nombre_usuario, $numero_departamento, $id);
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        header("Location: empleados.php");
        exit;
    }
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
        function showErrorMessages(errors) {
            for (const [field, message] of Object.entries(errors)) {
                alert(message);
            }
        }
        function toggleDepartamento() {
            var checkbox = document.getElementById('es_administrador');
            var departamentoSelect = document.getElementById('numero_departamento');

            if (checkbox.checked) {
                departamentoSelect.disabled = true;
                departamentoSelect.value = ''; 
            } else {
                departamentoSelect.disabled = false;
            }
        }
    </script>
</head>
<body class="alta">
<?php include 'n-barAdmin.php'; ?> 
<section class="home-section">
    <div class="encabezado">
        <img src="/img/descarga2.png" alt="Logo de TeLoCambio">
        <h1 class="titulo alta">Menú del Administrador</h1>
        </div>
    <h2>Sistema de modificacion de usuarios</h2><br>
    <div class="descripcion-turnos">
        <p>¡Bienvenido/a! Utilice el siguiente formulario para modificar a los trabajadores existentes.</p>
    </div><br>
    <div class="container-alta">
        <form action="" method="post" class="alta">
            <div class="input-group-alta">
                <label for="nombre" class="alta">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required pattern="[a-zA-Z\s]+" value="<?php echo htmlspecialchars($empleado['Nombre']); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="nombre_usuario" class="alta">Nombre de usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required pattern="\S+" value="<?php echo htmlspecialchars($empleado['Nombre_Usuario']); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="apellido" class="alta">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required pattern="[a-zA-Z\s]+" value="<?php echo htmlspecialchars($empleado['Apellido']); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="dni" class="alta">DNI:</label>
                <input type="text" id="dni" name="dni" required pattern="[0-9]{8}[A-Za-z]" value="<?php echo htmlspecialchars($empleado['DNI']); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="correo" class="alta">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($empleado['Correo_electronico']); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="contrasena" class="alta">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="es_administrador" class="alta">¿Es administrador?:</label>
                <input type="checkbox" id="es_administrador" name="es_administrador" <?php echo $empleado['Es_administrador'] ? 'checked' : ''; ?> class="alta" onchange="toggleDepartamento()">
            </div>
            <div class="input-group-alta">
                <label for="telefono" class="alta">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required pattern="[0-9]{9,15}" value="<?php echo htmlspecialchars($empleado['Telefono']); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="direccion" class="alta">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required value="<?php echo htmlspecialchars($empleado['Direccion']); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="numero_departamento" class="alta">Departamento:</label>
                <select id="numero_departamento" name="numero_departamento" required class="alta" <?php echo $empleado['Es_administrador'] ? 'disabled' : ''; ?>>
                    <option value=""> </option>
                    <?php foreach ($departamentos as $departamento): ?>
                        <option value="<?php echo $departamento['ID']; ?>" <?php echo $empleado['Numero_Departamento'] == $departamento['ID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($departamento['ID'] . ' - ' . $departamento['Nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" value="MODIFICAR" class="n-boton-alta">
        </form>
        <div>
        </div>
        <?php if (!empty($errors)): ?>
            <div class="error-message-alta">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="botones-container">
            <a href="empleados.php" class="n-boton">VOLVER</a>
    </div>
</body>
</html>


