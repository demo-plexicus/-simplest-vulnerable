<?php
// Conexión a la base de datos (modifica con tus propios parámetros de conexión)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_contraseña";
$dbname = "tu_base_de_datos";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

/*

 Soy Josue
 Se corrige la vulnerabilidad de SQL Injection:
 1) Se valida que 'id' sea un entero (lista blanca).
 2) Se usan sentencias preparadas (prepared statements) para evitar concatenación de SQL con entrada del usuario.
*/
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL
    // Validar que id sea un entero (lista blanca)
    if (!ctype_digit($id)) {
        echo "0 resultados";
    } else {
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        $int_id = (int)$id;
        if ($stmt) {
            $stmt->bind_param("i", $int_id);
            $stmt->execute();
            $stmt->bind_result($res_id, $res_nombre);
            $found = false;
            while ($stmt->fetch()) {
                $found = true;
                echo "id: " . $res_id . " - Nombre: " . $res_nombre . "<br>";
            }
            if (!$found) {
                echo "0 resultados";
            }
            $stmt->close();
        } else {
            // En caso de fallo al preparar la sentencia, no exponer detalles internos.
            echo "0 resultados";
        }
    }
}


// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
