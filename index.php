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

//Elena

// Manejo seguro del parámetro 'id' usando consultas preparadas y validación
if (isset($_GET['id'])) {
    // Validar y convertir el id a entero usando filter_input
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === false || $id === null) {
        // ID no válido: responder con un mensaje genérico
        echo "ID inválido";
    } else {
        // Preparar la consulta para evitar SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar cualquier dato proveniente de la base de datos antes de imprimirlo en HTML
                    $safe_id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                    $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // En caso de error al preparar la sentencia, no mostrar detalles internos
            echo "Error interno en la consulta";
        }
    }
}

// Manejo seguro del parámetro 'mensaje' para evitar XSS
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>"; // Escapar antes de imprimir
}

// Cerrar conexión
$conn->close();
?>
