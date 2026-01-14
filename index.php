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


// TECNALIA

// Manejo seguro de la entrada 'id' usando consultas preparadas y validación
if (isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado desde la URL

    // Validar que el id sea un número entero positivo (evita inputs maliciosos)
    if (!ctype_digit($id)) {
        echo "ID inválido.";
    } else {
        $id_int = (int)$id;

        // Usar una consulta preparada para evitar SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_int);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar los datos antes de imprimir para evitar XSS
                    $safe_id = htmlspecialchars($row['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // Error al preparar la consulta
            echo "Error en la consulta.";
        }
    }
}

// Manejo seguro del parámetro 'mensaje' para evitar XSS
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    $safe_mensaje = htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo "<div>" . $safe_mensaje . "</div>"; // Salida escapada
}

// Cerrar conexión
$conn->close();
?>
