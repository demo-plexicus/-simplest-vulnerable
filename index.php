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

require_once 'html_escape.php';

// Vulnerabilidad de SQL Injection
// El siguiente código es vulnerable a SQL Injection ya que el input del usuario se concatena directamente en la consulta SQL sin validación o sanitización.
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Use a prepared statement to avoid SQL injection.
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
    if ($stmt) {
        // Bind as integer when possible, otherwise bind as string.
        if (is_numeric($id)) {
            $id_param = (int)$id;
            $stmt->bind_param("i", $id_param);
        } else {
            $stmt->bind_param("s", $id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Encode output before echoing to prevent stored XSS.
                echo "id: " . h($row["id"]) . " - Nombre: " . h($row["nombre"]) . "<br>";
            }
        } else {
            echo "0 resultados";
        }
        $stmt->close();
    } else {
        // Do not expose raw SQL errors or fallback to unsafe concatenation.
        echo "Error en la consulta.";
    }
}

// Vulnerabilidad de Cross-Site Scripting (XSS)
// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>" . h($mensaje) . "</div>";
}

// Cerrar conexión

?>
