<?php
include 'connexio.php';

session_start();
if (!isset($_SESSION["usuario_id"])) {
    echo "Error: Debes iniciar sesión para acceder a esta página.";
    exit();
}

$id_usuario = $_SESSION["usuario_id"];

$sql = "SELECT * FROM Usuarios WHERE ID_USUARIO = $id_usuario";
$resultado = mysqli_query($connexio, $sql);

if (mysqli_num_rows($resultado) > 0) {
    $row = mysqli_fetch_assoc($resultado);
    $tipo_usuario = $row["TIPO_USUARIO"];
    $nombre = $row["NOMBRE"];
    $mail = $row["MAIL"];
    $f_nacimiento = $row["F_NACIMIENTO"];
    $foto = $row["FOTO"];
} else {
    echo "No se encontró ningún usuario con ID $id_usuario.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["guardar"])) {
    $nombre_nuevo = $_POST["nombre"];
    $mail_nuevo = $_POST["mail"];
    $f_nacimiento_nuevo = $_POST["f_nacimiento"];
    $foto_nueva = $_FILES["foto"]["tmp_name"];

    $sql_update = "UPDATE Usuarios SET NOMBRE = '$nombre_nuevo', MAIL = '$mail_nuevo', F_NACIMIENTO = '$f_nacimiento_nuevo'";
    
    if (!empty($foto_nueva)) {
        $foto_nueva_contenido = addslashes(file_get_contents($foto_nueva));
        $sql_update .= ", FOTO = '$foto_nueva_contenido'";
    }

    $sql_update .= " WHERE ID_USUARIO = $id_usuario";

    if (mysqli_query($connexio, $sql_update)) {
        echo "Datos actualizados correctamente.";
    } else {
        echo "Error al actualizar los datos del usuario: " . mysqli_error($connexio);
    }
}

mysqli_close($connexio);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
</head>
<body>
    <h2>Perfil de Usuario</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" value="<?php echo $nombre; ?>"><br><br>

        <label for="mail">Correo Electrónico:</label><br>
        <input type="email" id="mail" name="mail" value="<?php echo $mail; ?>"><br><br>

        <label for="f_nacimiento">Fecha de Nacimiento:</label><br>
        <input type="date" id="f_nacimiento" name="f_nacimiento" value="<?php echo $f_nacimiento; ?>"><br><br>

        <label for="foto">Foto de Perfil:</label><br>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($foto); ?>" width="100" height="100" alt="Foto de Perfil"><br>
        <input type="file" id="foto" name="foto"><br><br>

        <input type="submit" name="guardar" value="Guardar">
    </form>
</body>
</html>
