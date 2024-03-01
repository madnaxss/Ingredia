<?php
include 'connexio.php';

session_start();
if (!isset($_SESSION["usuario_id"])) {
    echo "Error: Debes iniciar sesión para agregar una receta.";
    exit();
}

$id_usuario = $_SESSION["usuario_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_ingrediente"])) {
    $nombre = $_POST["nombre"];
    $calorias = $_POST["calorias"];
    $proteinas = $_POST["proteinas"];

    $imagen = $_FILES["imagen"]["tmp_name"];
    $imagen_contenido = addslashes(file_get_contents($imagen));

    $sql = "INSERT INTO Ingredientes (NOMBRE, CALORIAS, PROTEINAS, FOTO) VALUES ('$nombre', '$calorias', '$proteinas', '$imagen_contenido')";

    if (mysqli_query($connexio, $sql)) {
        echo "Ingrediente agregado exitosamente.";
    } else {
        echo "Error al agregar el ingrediente: " . $sql . "<br>" . mysqli_error($connexio);
    }
}

mysqli_close($connexio);
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Ingredientes</title>
</head>
<body>
    <h2>Agregar Ingrediente</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required><br><br>

        <label for="calorias">Calorías:</label><br>
        <input type="number" id="calorias" name="calorias" step="0.01" required><br><br>

        <label for="proteinas">Proteínas:</label><br>
        <input type="number" id="proteinas" name="proteinas" step="0.01" required><br><br>

        <label for="imagen">Imagen:</label><br>
        <input type="file" id="imagen" name="imagen" accept="image/*" required><br><br>

        <input type="submit" name="agregar_ingrediente" value="Agregar Ingrediente">
    </form>
</body>
</html>
