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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $dni = trim($_POST['dni']);
    $correo = trim($_POST['correo']);
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);
    $es_administrador = isset($_POST['es_administrador']) ? 1 : 0;
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $numero_departamento = $_POST['numero_departamento'];

    if (!validDni::check($dni)) {
        $errors['dni'] = "<br>*El DNI no es válido.";
    }

    if (empty($errors)) {
        $stmt = $conexion->prepare("SELECT ID FROM trabajador WHERE DNI = ?");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['dni'] = "<br>*El DNI ya está registrado.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conexion->prepare("SELECT ID FROM trabajador WHERE Correo_electronico = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['correo'] = "<br>*El correo electrónico ya está registrado.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $stmt = $conexion->prepare("SELECT ID FROM trabajador WHERE Nombre_Usuario = ?");
        $stmt->bind_param("s", $nombre_usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['nombre_usuario'] = "<br>*El nombre de usuario ya está en uso.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        $sql = "INSERT INTO trabajador (Nombre, Apellido, DNI, Correo_electronico, Contrasena, Es_administrador, Telefono, Direccion, Nombre_Usuario, Numero_Departamento) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("sssssiissi", $nombre, $apellido, $dni, $correo, $contrasena, $es_administrador, $telefono, $direccion, $nombre_usuario, $numero_departamento);

        if ($stmt->execute()) {
            echo "<script>alert('Trabajador añadido exitosamente.'); 
                          window.location.href = 'altaUsuarios.php';</script>";
        } else {
            echo "<script>alert('Error: " . $sql . "<br>" . $conexion->error . "');</script>";
        }
        $stmt->close();
        $conexion->close();
    }
}
?>
<!DOCTYPE html>
z<html lang="es">
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
    <h2>Sistema de gestión de alta de usuarios</h2><br>
    <div class="descripcion-turnos">
        <p>¡Bienvenido/a! Utilice el siguiente formulario para dar de alta a nuevos trabajadores.</p>
    </div><br>
    <div class="container-alta">
        <form action="" method="post" class="alta">
            <div class="input-group-alta">
                <label for="nombre" class="alta">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required pattern="[a-zA-Z\s]+" value="<?php echo htmlspecialchars($nombre); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="nombre_usuario" class="alta">Nombre de usuario:</label>
                <input type="text" id="nombre_usuario" name="nombre_usuario" required pattern="\S+" value="<?php echo htmlspecialchars($nombre_usuario); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="apellido" class="alta">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required pattern="[a-zA-Z\s]+" value="<?php echo htmlspecialchars($apellido); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="dni" class="alta">DNI:</label>
                <input type="text" id="dni" name="dni" required pattern="[0-9]{8}[A-Za-z]" value="<?php echo htmlspecialchars($dni); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="correo" class="alta">Correo electrónico:</label>
                <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($correo); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="contrasena" class="alta">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required class="alta">
            </div>
            <div class="input-group-alta">
                <label for="es_administrador" class="alta">¿Es administrador?:</label>
                <input type="checkbox" id="es_administrador" name="es_administrador" <?php echo $es_administrador ? 'checked' : ''; ?> class="alta" onchange="toggleDepartamento()">
            </div>
            <div class="input-group-alta">
                <label for="telefono" class="alta">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required pattern="[0-9]{9,15}" value="<?php echo htmlspecialchars($telefono); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="direccion" class="alta">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required value="<?php echo htmlspecialchars($direccion); ?>" class="alta">
            </div>
            <div class="input-group-alta">
                <label for="numero_departamento" class="alta">Departamento:</label>
                <select id="numero_departamento" name="numero_departamento" required class="alta">
                    <option value=""> </option>
                    <?php foreach ($departamentos as $departamento): ?>
                        <option value="<?php echo $departamento['ID']; ?>" <?php echo $numero_departamento == $departamento['ID'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($departamento['ID'] . ' - ' . $departamento['Nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="submit" value="REGISTRAR EN EL SISTEMA" class="n-boton-alta">
        </form>
        <?php if (!empty($errors)): ?>
            <div class="error-message-alta">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div><br>

    <div class="form-container-alta">
    <h4>RESTRICCIONES AL CREAR USUARIOS</h4><br>
    <ul>
        <li>Todos los campos son obligatorios.</li>
        <li>Nombre y Apellido: Solo se admiten caracteres alfabéticos y espacios.</li>
        <li>Nombre de usuario: No se permiten espacios en blanco.</li>
        <li>Teléfono: Debe contener entre 9 y 15 dígitos numéricos.</li>
    </ul>
    </div>
</body>
</html>
