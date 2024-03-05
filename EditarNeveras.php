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

// Obtener el ID de la nevera de la URL
if (isset($_GET['id_nevera'])) {
    $id_nevera = $_GET['id_nevera'];
} else {
    // Si no se proporciona un ID de nevera, redirigir al usuario a otra página o mostrar un mensaje de error
    header("Location: error.php"); // Cambia "error.php" por la URL de tu página de error
    exit();
}

// Consulta SQL para obtener los ingredientes dentro de la nevera seleccionada por el usuario
$sql_ingredientes_nevera = "SELECT i.ID_INGREDIENTE, i.NOMBRE, i.FOTO 
                            FROM Ingredientes_Neveras in_nv 
                            INNER JOIN Ingredientes i ON in_nv.ID_INGREDIENTE = i.ID_INGREDIENTE 
                            WHERE in_nv.ID_NEVERA = $id_nevera";
$resultado_ingredientes_nevera = mysqli_query($connexio, $sql_ingredientes_nevera);

// Consulta SQL para obtener todos los ingredientes disponibles para agregar a la nevera
$sql_ingredientes_disponibles = "SELECT ID_INGREDIENTE, NOMBRE, FOTO 
                                 FROM Ingredientes 
                                 WHERE ID_INGREDIENTE NOT IN 
                                 (SELECT ID_INGREDIENTE FROM Ingredientes_Neveras WHERE ID_NEVERA = $id_nevera)";
$resultado_ingredientes_disponibles = mysqli_query($connexio, $sql_ingredientes_disponibles);

// Procesar el formulario para agregar ingredientes a la nevera
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_ingrediente"])) {
    $id_ingrediente = $_POST["id_ingrediente"];
    echo 'agrega el ingrediente';

    // Consulta SQL para insertar el ingrediente en la nevera
    $sql_insertar_ingrediente = "INSERT INTO Ingredientes_Neveras (ID_NEVERA, ID_INGREDIENTE) 
                                 VALUES ($id_nevera, $id_ingrediente)";
    
    if (mysqli_query($connexio, $sql_insertar_ingrediente)) {
        echo "Ingrediente agregado correctamente a la nevera.";
        header("Location: {$_SERVER['PHP_SELF']}?id_nevera=$id_nevera");
        exit();
    } else {
        echo "Error al agregar el ingrediente a la nevera: " . mysqli_error($connexio);
    }
}

// Procesar el formulario para eliminar ingredientes de la nevera
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_ingrediente"])) {
    $id_ingrediente_eliminar = $_POST["id_ingrediente_eliminar"];
    
    // Consulta SQL para eliminar el ingrediente de la nevera
    $sql_eliminar_ingrediente = "DELETE FROM Ingredientes_Neveras WHERE ID_NEVERA = $id_nevera AND ID_INGREDIENTE = $id_ingrediente_eliminar";
    
    if (mysqli_query($connexio, $sql_eliminar_ingrediente)) {
        echo "Ingrediente eliminado correctamente de la nevera.";
        header("Location: {$_SERVER['PHP_SELF']}?id_nevera=$id_nevera");
        exit();
    } else {
        echo "Error al eliminar el ingrediente de la nevera: " . mysqli_error($connexio);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Nevera</title>
</head>
<body>
    <h2>Editar Nevera</h2>
    <h3>Ingredientes en la Nevera:</h3>
    <ul>
        <?php
        if ($resultado_ingredientes_nevera && mysqli_num_rows($resultado_ingredientes_nevera) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado_ingredientes_nevera)) {
                echo "<li>" . $fila['NOMBRE'] . "
                      <form method='post'>
                        <input type='hidden' name='id_ingrediente_eliminar' value='" . $fila['ID_INGREDIENTE'] . "'>
                        <button type='submit' name='eliminar_ingrediente'>Eliminar</button>
                      </form>
                      </li>";
            }
        } else {
            echo "<li>No hay ingredientes en esta nevera.</li>";
        }
        ?>
    </ul>

    <h3>Agregar Ingrediente:</h3>
    <ul>
        <?php
        if ($resultado_ingredientes_disponibles && mysqli_num_rows($resultado_ingredientes_disponibles) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado_ingredientes_disponibles)) {
                echo "<li><img src='data:image/jpeg;base64," . base64_encode($fila['FOTO']) . "' alt='" . $fila['NOMBRE'] . "'><br>" . $fila['NOMBRE'] . " 
                      <form method='post'>
                        <input type='hidden' name='id_ingrediente' value='" . $fila['ID_INGREDIENTE'] . "'>
                        <button type='submit' name='agregar_ingrediente'>Agregar</button>
                      </form>
                      </li>";
            }
        }
        ?>
    </ul>
</body>
</html>




