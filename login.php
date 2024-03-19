<?php
include 'connexio.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["iniciar_sesion"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT ID_USUARIO, MAIL, CONTRASENA FROM Usuarios WHERE MAIL = '$email'";
    $result = mysqli_query($connexio, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashed_password = $row["CONTRASENA"];
        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION["usuario_id"] = $row["ID_USUARIO"];
            echo "Inicio de sesión exitoso. ¡Bienvenido!";
        } else {
            echo "Contraseña incorrecta. Por favor, inténtalo de nuevo.";
        }
    } else {
        echo "Usuario no encontrado. Por favor, regístrate primero.";
    }
}

mysqli_close($connexio);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="estilo.css">
    <link href="https://fonts.googleapis.com/css2?family=M+PLUS+Rounded+1c&display=swap" rel="stylesheet">
</head>

<body>
    <section id="inicioSesion">
        <div class="divGeneralInicioSesion">
            <h2>Iniciar Sesión</h2>
            <form action="" method="POST">
                <input type="email" id="email" name="email" required placeholder="Email"><br><br>

                <input type="password" id="password" name="password" required placeholder="Contraseña"><br><br>
            </form>
            <div class="notienescuentaHasolvidado">
                <div class="noTienesCuenta">
                    <p>No tienes cuenta? <span>Regístrate</span></p>
                </div>
                <div>
                    <p>Has olvidado la contraseña?</p>
                </div>
            </div>
            <form>
                <input type="submit" name="iniciar_sesion" id="bt" value="Iniciar Sesión">
            </form>
        </div>
    </section>
</body>

</html>