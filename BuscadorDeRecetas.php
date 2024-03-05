<?php
include 'connexio.php';

session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['usuario_id'];

$sql_ingredientes_disponibles = "SELECT i.ID, i.NOMBRE, i.FOTO 
                                 FROM Ingredientes i
                                 WHERE NOT EXISTS (
                                     SELECT 1 
                                     FROM Ingredientes_Seleccionados isel 
                                     WHERE isel.ID_INGREDIENTE = i.ID
                                     AND isel.ID_USUARIO = $id
                                 )";
$resultado_ingredientes_disponibles = mysqli_query($connexio, $sql_ingredientes_disponibles);

$sql_ingredientes_seleccionados = "SELECT i.ID, i.NOMBRE, i.FOTO
                                   FROM Ingredientes i
                                   INNER JOIN Ingredientes_Seleccionados isel ON i.ID = isel.ID_INGREDIENTE
                                   WHERE isel.ID_USUARIO = $id";
$resultado_ingredientes_seleccionados = mysqli_query($connexio, $sql_ingredientes_seleccionados);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_ingrediente_seleccionado"])) {
    $id_ingrediente = $_POST["id_ingrediente"];

    $sql_insertar_ingrediente = "INSERT INTO Ingredientes_Seleccionados (ID_USUARIO, ID_INGREDIENTE) VALUES ($id, $id_ingrediente)";
    
    if (mysqli_query($connexio, $sql_insertar_ingrediente)) {
        header("Location: BuscadorDeRecetas.php"); 
        exit();
    } else {
        echo "Error al agregar el ingrediente: " . mysqli_error($connexio);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["eliminar_ingrediente_seleccionado"])) {
    $id_ingrediente_eliminar = $_POST["id_ingrediente_eliminar"];

    $sql_eliminar_ingrediente = "DELETE FROM Ingredientes_Seleccionados WHERE ID_USUARIO = $id AND ID_INGREDIENTE = $id_ingrediente_eliminar";
    
    if (mysqli_query($connexio, $sql_eliminar_ingrediente)) {
        header("Location: BuscadorDeRecetas.php"); 
        exit();
    } else {
        echo "Error al eliminar el ingrediente: " . mysqli_error($connexio);
    }
}

$sql_recetas_con_ingredientes_seleccionados = "SELECT r.ID, r.NOMBRE, r.DESCRIPCION
                                               FROM Recetas r
                                               INNER JOIN Recetas_Ingredientes ri ON r.ID = ri.ID_RECETA
                                               INNER JOIN Ingredientes_Seleccionados isel ON ri.ID_INGREDIENTE = isel.ID_INGREDIENTE
                                               WHERE isel.ID_USUARIO = $id
                                               GROUP BY r.ID
                                               HAVING COUNT(*) = (
                                                   SELECT COUNT(*)
                                                   FROM Ingredientes_Seleccionados isel
                                                   WHERE isel.ID_USUARIO = $id
                                               )";
$resultado_recetas_con_ingredientes_seleccionados = mysqli_query($connexio, $sql_recetas_con_ingredientes_seleccionados);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Ingredientes</title>
</head>
<body>
    <h2>Seleccionar Ingredientes</h2>

    <h3>Ingredientes Disponibles:</h3>
    <ul>
        <?php
        if ($resultado_ingredientes_disponibles && mysqli_num_rows($resultado_ingredientes_disponibles) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado_ingredientes_disponibles)) {
                echo "<li><img src='data:image/jpeg;base64," . base64_encode($fila['FOTO']) . "' alt='" . $fila['NOMBRE'] . "'><br>" . $fila['NOMBRE'] . " 
                      <form method='post'>
                        <input type='hidden' name='id_ingrediente' value='" . $fila['ID'] . "'>
                        <button type='submit' name='agregar_ingrediente_seleccionado'>Añadir</button>
                      </form>
                      </li>";
            }
        } else {
            echo "<li>No hay ingredientes disponibles.</li>";
        }
        ?>
    </ul>

    <h3>Ingredientes Seleccionados:</h3>
    <ul>
        <?php
        if ($resultado_ingredientes_seleccionados && mysqli_num_rows($resultado_ingredientes_seleccionados) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado_ingredientes_seleccionados)) {
                echo "<li><img src='data:image/jpeg;base64," . base64_encode($fila['FOTO']) . "' alt='" . $fila['NOMBRE'] . "'><br>" . $fila['NOMBRE'] . " 
                      <form method='post'>
                        <input type='hidden' name='id_ingrediente_eliminar' value='" . $fila['ID'] . "'>
                        <button type='submit' name='eliminar_ingrediente_seleccionado'>Eliminar</button>
                      </form>
                      </li>";
            }
        } else {
            echo "<li>No hay ingredientes seleccionados.</li>";
        }
        ?>
    </ul>

    <h3>Recetas con Ingredientes Seleccionados:</h3>
    <ul>
        <?php
        if ($resultado_recetas_con_ingredientes_seleccionados && mysqli_num_rows($resultado_recetas_con_ingredientes_seleccionados) > 0) {
            while ($fila = mysqli_fetch_assoc($resultado_recetas_con_ingredientes_seleccionados)) {
                echo "<li><strong>Nombre:</strong> " . $fila['NOMBRE'] . "<br><strong>Descripción:</strong> " . $fila['DESCRIPCION'] . "</li>";
            }
        } else {
            echo "<li>No hay recetas disponibles con los ingredientes seleccionados.</li>";
        }
        ?>
    </ul>
</body>
</html>




