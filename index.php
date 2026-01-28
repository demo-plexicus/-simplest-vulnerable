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
// Danny, estoy lo aceptamos, pero me gustaria validar id de otra forma
// Manejo seguro del parámetro id: validación y uso de sentencia preparada
if (isset($_GET['id'])) {
    // Validar que 'id' es un entero
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id === false || $id === null) {
        // Manejo de entrada inválida
        echo "ID inválido.";
    } else {
        // Preparar sentencia parametrizada para prevenir SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            // Manejo de error en la preparación de la sentencia
            error_log("Prepare failed: " . $conn->error);
            echo "Error en la consulta.";
        } else {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar la salida para prevenir XSS
                    $safe_id = htmlspecialchars($row["id"], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $safe_nombre = htmlspecialchars($row["nombre"], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        }
    }
}

// Manejo seguro del mensaje: escapar antes de imprimir para prevenir XSS
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $safe_mensaje = htmlspecialchars($mensaje, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo "<div>" . $safe_mensaje . "</div>";
}

// Cerrar conexión
$conn->close();
?>
