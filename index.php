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

// EEGSA

// Vulnerabilidad de SQL Injection mitigada
// Ahora se utiliza una sentencia preparada y validación estricta del parámetro 'id' para prevenir SQL Injection.
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Validación: asegurarse de que 'id' es un entero
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        echo "ID inválido";
    } else {
        // Defensa en profundidad: coerción explícita a entero
        $id_int = (int)$id;

        // Preparar la sentencia para evitar concatenación de entradas del usuario en la consulta SQL
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_int);
            $stmt->execute();

            // Vincular resultados y obtenerlos de forma segura
            $stmt->bind_result($res_id, $res_nombre);
            $found = false;
            while ($stmt->fetch()) {
                $found = true;
                // Escapar/encodear la salida para prevenir XSS reflejado
                echo "id: " . htmlspecialchars($res_id, ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($res_nombre, ENT_QUOTES, 'UTF-8') . "<br>";
            }

            if (!$found) {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // En caso de error al preparar la consulta, no exponer detalles sensibles
            echo "Error en la consulta";
        }
    }
}

// Vulnerabilidad de Cross-Site Scripting (XSS) mitigada
// Se encodea la salida para evitar la inyección de HTML/JS cuando se muestra 'mensaje'.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>"; // Salida segura
}

// Cerrar conexión
$conn->close();
?>
