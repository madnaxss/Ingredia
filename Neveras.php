<?php
include 'connexio.php';

// Verificar si el usuario está autenticado y obtener su ID de usuario de la sesión
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Si el usuario no está autenticado, redirigirlo a la página de inicio de sesión o mostrar un mensaje de error
    header("Location: login.php"); // Cambia "login.php" por la URL de tu página de inicio de sesión
    exit();
}

$id_usuario = $_SESSION['usuario_id']; // Obtener el ID del usuario de la sesión

$sql_neveras = "SELECT ID_NEVERA, NOMBRE FROM Neveras WHERE ID_USUARIO = $id_usuario";
$resultado_neveras = mysqli_query($connexio, $sql_neveras);

if ($resultado_neveras && mysqli_num_rows($resultado_neveras) > 0) {
    while ($fila = mysqli_fetch_assoc($resultado_neveras)) {
        echo "<h3>" . $fila['NOMBRE'] . ":</h3>";
        echo "<a href='EditarNevera.php?id_nevera=" . $fila['ID_NEVERA'] . "'>Editar</a><br>";
    }
} else {
    echo "El usuario no tiene ninguna nevera.";
}

mysqli_free_result($resultado_neveras);

mysqli_close($connexio);
?>
