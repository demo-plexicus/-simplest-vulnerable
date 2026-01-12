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

// Manejo seguro de la consulta usando prepared statements y validación
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    // Validar que sea un entero
    if (filter_var($id, FILTER_VALIDATE_INT) === false) {
        echo "ID inválido";
    } else {
        $id = (int)$id;
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar la salida para evitar XSS desde contenido de la BD
                    echo "id: " . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8') . "<br>";
                }
            } else {
                echo "0 resultados";
            }
            $stmt->close();
        } else {
            // Mensaje de error genérico sin filtrar detalles de la base de datos
            echo "Error en la consulta";
        }
    }
}

// Manejo seguro de la entrada para evitar Cross-Site Scripting (XSS)
// Escapar cualquier salida que provenga de input del usuario antes de imprimirla en HTML.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    // Escapar el mensaje para prevenir inyección de HTML/JS en la página
    echo '<div>' . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '</div>';
}

// Cerrar conexión
$conn->close();
?>
