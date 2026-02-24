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

/* Vulnerabilidad de SQL Injection
   Reemplazamos la concatenación insegura por una sentencia preparada, validamos que el id
   sea un entero positivo (solo dígitos) y escapamos la salida de la base de datos para evitar XSS. */
if (isset($_GET['id'])) {
    $id_raw = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Validar que el id sea únicamente dígitos (enteros no negativos)
    if (!ctype_digit($id_raw)) {
        // Entrada inválida: evitar exponer información sensible
        echo "Parámetro 'id' inválido.";
    } else {
        $id = (int)$id_raw;

        // Preparar la consulta para evitar SQL Injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Escapar valores antes de imprimir para prevenir XSS reflejado
                    $safe_id = htmlspecialchars((string)$row["id"], ENT_QUOTES, 'UTF-8');
                    $safe_nombre = htmlspecialchars((string)$row["nombre"], ENT_QUOTES, 'UTF-8');
                    echo "id: " . $safe_id . " - Nombre: " . $safe_nombre . "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // Fallo al preparar la consulta: no exponer detalles del error en producción
            echo "Error en la consulta.";
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
