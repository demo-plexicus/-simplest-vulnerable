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
// EXAMPLE HERE BIP
// Manejo seguro de entrada para prevenir SQL Injection
// Usar sentencias preparadas y validación de tipo
if (isset($_GET['id'])) {
    $id_raw = $_GET['id'];
    if (!ctype_digit($id_raw)) {
        echo "ID inválido";
    } else {
        $id = (int) $id_raw;
        if ($stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?")) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $safe_id = htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8');
                    $safe_nombre = htmlspecialchars($row["nombre"], ENT_QUOTES, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }
            $stmt->close();
        } else {
            // Fallback: consulta preparada no pudo ser creada
            echo "Error en la consulta.";
        }
    }
}

// Mitigación de XSS: escapar salida antes de imprimir
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}

// Cerrar conexión
$conn->close();
?>
