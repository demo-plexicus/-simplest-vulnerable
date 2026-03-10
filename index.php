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

// Vulnerabilidad de SQL Injection
// El siguiente código es vulnerable a SQL Injection ya que el input del usuario se concatena directamente en la consulta SQL sin validación o sanitización.
if(isset($_GET['id'])) {
    // cr0hn example
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL
    // Validar que el id sea un entero positivo (lista blanca)
    if (!preg_match('/^\d+$/', $id)) {
        echo "ID inválido";
    } else {
        $id = (int)$id;
        // Usar prepared statement para evitar SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($row_id, $row_nombre);
                while ($stmt->fetch()) {
                    echo "id: " . $row_id . " - Nombre: " . $row_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }
            $stmt->close();
        } else {
            error_log("Prepare failed: " . $conn->error);
            echo "Error en la consulta";
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
