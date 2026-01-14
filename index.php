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

// ejemplo botech

// Manejo seguro de la consulta por ID usando prepared statements y validación
if (isset($_GET['id'])) {
    // Validar que el id es un entero xxxxxx
    $id_raw = $_GET['id'];
    $id = filter_var($id_raw, FILTER_VALIDATE_INT);

    if ($id === false) {
        // ID inválido: no ejecutar ninguna consulta y devolver mensaje seguro
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
                    // Escapar la salida para evitar XSS
                    $nombre_esc = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');
                    echo "id: " . $row["id"] . " - Nombre: " . $nombre_esc . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // Manejo de error en la preparación de la consulta
            error_log("Error al preparar la consulta: " . $conn->error);
            echo "Ocurrió un error al procesar la solicitud.";
        }
    }
}

// Mitigación de Cross-Site Scripting (XSS)
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    // Escapar cualquier contenido antes de imprimir en HTML
    $mensaje_esc = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    echo "<div>" . $mensaje_esc . "</div>";
}

// Cerrar conexión
$conn->close();
?>
