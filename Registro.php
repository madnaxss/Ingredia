<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
</head>
<body>
    <h2>Registro de Usuario</h2>
    <form action="" method="POST">
        <label for="email">Correo Electrónico:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <label for="confirm_password">Confirmar Contraseña:</label><br>
        <input type="password" name="confirm_password" required><br><br>

        <input type="submit" name="registrarse" value="Registrarse">
    </form>
</body>
</html> 
<?php
include 'connexio.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registrarse"])) {
    // Recibir los datos del formulario
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Verificar si las contraseñas coinciden
    if ($password != $confirm_password) {
        echo "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
        exit();
    }

    // Hash de la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Verificar la conexión
    if (!$connexio) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    // Consulta SQL para insertar los datos en la tabla de usuarios
    $sql = "INSERT INTO Usuarios (MAIL, CONTRASENA) VALUES ('$email', '$hashed_password')";

    // Ejecutar la consulta para insertar el usuario
    if (mysqli_query($connexio, $sql)) {
        echo "Registro exitoso. ¡Bienvenido!";

        // Obtener el ID del usuario recién insertado
        $id_usuario = mysqli_insert_id($connexio);

        // Crear tres neveras vacías para el usuario
        for ($i = 1; $i <= 3; $i++) {
            $nombre_nevera = "Nevera " . $i;

            // Consulta SQL para insertar la nevera
            $sql_nevera = "INSERT INTO Neveras (NOMBRE, ID_USUARIO) VALUES ('$nombre_nevera', '$id_usuario')";

            // Ejecutar la consulta para insertar la nevera
            if (!mysqli_query($connexio, $sql_nevera)) {
                echo "Error al crear la nevera: " . mysqli_error($connexio);
            }
        }
    } else {
        echo "Error al registrar el usuario: " . mysqli_error($connexio);
    }

    // Cerrar la conexión
    mysqli_close($connexio);
}
?>


