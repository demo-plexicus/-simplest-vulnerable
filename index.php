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

// SECUREIT

// Manejo seguro de entrada y consultas (protección contra SQL Injection y XSS)
// Usar declaraciones preparadas y validación estricta de entrada
if (isset($_GET['id'])) {
    // Validar que 'id' sea un entero válido
    $id = $_GET['id'];
    if (filter_var($id, FILTER_VALIDATE_INT) === false) {
        // Registrar intento sospechoso y retornar mensaje genérico
        error_log("Entrada inválida para id: " . $id);
        echo "Parámetro 'id' inválido.";
    } else {
        $id_int = (int)$id;
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt === false) {
            error_log("Fallo al preparar la consulta: " . $conn->error);
            echo "Error interno.";
        } else {
            $stmt->bind_param("i", $id_int);
            if (!$stmt->execute()) {
                error_log("Fallo al ejecutar la consulta: " . $stmt->error);
                echo "Error interno.";
            } else {
                $result = $stmt->get_result();
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $safe_id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                        $safe_nombre = htmlspecialchars($row['nombre'], ENT_QUOTES, 'UTF-8');
                        echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                    }
                } else {
                    echo "0 resultados";
                }
            }
            $stmt->close();
        }
    }
}

// Protección contra XSS para parámetros de mensajes
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $safe_mensaje = htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8');
    echo "<div>" . $safe_mensaje . "</div>";
}

// Cerrar conexión
$conn->close();
?>
