<?php
include 'connexio.php';

// Verificar si el usuario está autenticado y obtener su ID de usuario de la sesión
session_start();
if (!isset($_SESSION['usuario_id'])) {
    // Si el usuario no está autenticado, redirigirlo a la página de inicio de sesión o mostrar un mensaje de error
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id']; // Obtener el ID del usuario de la sesión

// Procesar el formulario para crear la receta
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["crear_receta"])) {

    $nombre_receta = $_POST['nombre_receta'];
    $descripcion_receta = $_POST['descripcion_receta'];

    // Consulta SQL para insertar la receta
    $sql_insertar_receta = "INSERT INTO Recetas (ID_USUARIO, NOMBRE, DESCRIPCION) 
                            VALUES ($id_usuario, '$nombre_receta', '$descripcion_receta')";

    if (mysqli_query($connexio, $sql_insertar_receta)) {
        // Obtener el ID de la receta recién creada
        $id_receta = mysqli_insert_id($connexio);
        $_SESSION['id_receta'] = $id_receta; // Guardar el ID de la receta en la sesión

        // Recorrer los checkboxes seleccionados y agregar los ingredientes a la receta
        if(isset($_POST['ingredientes'])){
            foreach ($_POST['ingredientes'] as $id_ingrediente) {
                // Consulta SQL para insertar el ingrediente en la receta
                $sql_insertar_ingrediente = "INSERT INTO recetas_ingredientes (ID_RECETA, ID_INGREDIENTE) 
                                             VALUES ($id_receta, $id_ingrediente)";
                
                if (mysqli_query($connexio, $sql_insertar_ingrediente)) {
                    echo "Ingrediente agregado correctamente a la receta.";
                } else {
                    echo "Error al agregar el ingrediente a la receta: " . mysqli_error($connexio);
                }
            }
        }

        echo "Receta creada correctamente.";
    } else {
        echo "Error al crear la receta: " . mysqli_error($connexio);
    }
}

// Consulta SQL para obtener todos los ingredientes disponibles
$sql_ingredientes_disponibles = "SELECT ID_INGREDIENTE, NOMBRE FROM Ingredientes";
$resultado_ingredientes_disponibles = mysqli_query($connexio, $sql_ingredientes_disponibles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Receta</title>
    <script>
        function moverIngrediente(checkbox) {
            var ingredientesSeleccionados = document.getElementById("ingredientesSeleccionados");
            var ingredientesDisponibles = document.getElementById("ingredientesDisponibles");
            var li = checkbox.parentNode;
            
            if (checkbox.checked) {
                ingredientesSeleccionados.appendChild(li);
            } else {
                ingredientesDisponibles.appendChild(li);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            var input = document.getElementById("buscarIngredientes");
            input.addEventListener("input", function() {
                var filter = input.value.toUpperCase();
                var ul = document.getElementById("ingredientesDisponibles");
                var li = ul.getElementsByTagName('li');
                for (var i = 0; i < li.length; i++) {
                    var a = li[i].getElementsByTagName("input")[0];
                    var txtValue = a.nextSibling.textContent || a.nextSibling.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        li[i].style.display = "";
                    } else {
                        li[i].style.display = "none";
                    }
                }
            });
        });
    </script>
</head>
<body>
    <h2>Crear Receta</h2>
    <form method="post">
        <label for="nombre_receta">Nombre de la Receta:</label><br>
        <input type="text" id="nombre_receta" name="nombre_receta" required><br><br>
        
        <label for="descripcion_receta">Descripción de la Receta:</label><br>
        <textarea id="descripcion_receta" name="descripcion_receta" required></textarea><br><br>

        <h3>Ingredientes Seleccionados:</h3>
        <ul id="ingredientesSeleccionados">
            <!-- Aquí se mostrarán los ingredientes seleccionados -->
        </ul>

        <h3>Ingredientes Disponibles:</h3>
        <input type="text" id="buscarIngredientes" placeholder="Buscar ingredientes...">
        <ul id="ingredientesDisponibles">
            <!-- Aquí se mostrarán los ingredientes disponibles -->
            <?php
            // Iterar sobre los ingredientes disponibles y generar los checkboxes
            while ($fila = mysqli_fetch_assoc($resultado_ingredientes_disponibles)) {
                echo "<li><input type='checkbox' onchange='moverIngrediente(this)' name='ingredientes[]' value='" . $fila['ID_INGREDIENTE'] . "'>" . $fila['NOMBRE'] . "</li>";
            }
            ?>
        </ul>

        <button type="submit" name="crear_receta">Crear Receta</button>
    </form>

    <!-- Formulario para añadir pasos -->
    <h2>Añadir Pasos a la Receta</h2>
    <?php
    // Verificar si hay una receta creada en la sesión
    if(isset($_SESSION['id_receta'])) {
        $id_receta = $_SESSION['id_receta'];

        // Mostrar el formulario para añadir pasos
        echo "<form method='post' action=''>";
        echo "<input type='hidden' name='id_receta' value='$id_receta'>";
        echo "<label for='num_paso'>Número del Paso:</label><br>";
        echo "<input type='text' id='num_paso' name='num_paso' required><br><br>";
        echo "<label for='desc_paso'>Descripción del Paso:</label><br>";
        echo "<textarea id='desc_paso' name='desc_paso' required></textarea><br><br>";
        echo "<button type='submit' name='agregar_paso'>Agregar Paso</button>";
        echo "</form>";

        // Procesar el formulario para agregar los pasos
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["agregar_paso"])) {
            $id_receta = $_POST['id_receta'];
            $num_paso = $_POST['num_paso'];
            $desc_paso = $_POST['desc_paso'];

            // Consulta SQL para insertar el paso en la receta
            $sql_insertar_paso = "INSERT INTO pasos (ID_RECETA, NUM, DESC_PASO) 
                                  VALUES ($id_receta, $num_paso, '$desc_paso')";
            
            if (mysqli_query($connexio, $sql_insertar_paso)) {
                echo "Paso agregado correctamente a la receta.";
            } else {
                echo "Error al agregar el paso a la receta: " . mysqli_error($connexio);
            }
        }
    } else {
        echo "No se ha creado ninguna receta.";
    }
    ?>
</body>
</html>
