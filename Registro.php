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
echo 'entra aqui';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["registrarse"])) {
    
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password != $confirm_password) {
        echo "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (!$connexio) {
        die("Error de conexión: " . mysqli_connect_error());
    }

    $sql = "INSERT INTO Usuarios (MAIL, CONTRASENA) VALUES ('$email', '$hashed_password')";

    if (mysqli_query($connexio, $sql)) {
        echo "Registro exitoso. ¡Bienvenido!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($connexio);
    }

    mysqli_close($connexio);
}
?>


