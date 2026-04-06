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

// Vulnerabilidad de SQL Injection - CORREGIDA
// Se utiliza una consulta preparada para evitar la inyección SQL.
if(isset($_GET['id'])) {
    // Validar que el id sea numérico (opcional, pero recomendado para este caso)
    $id = $_GET['id'];
    if (!is_numeric($id)) {
        echo "Error: el parámetro id debe ser numérico";
        exit;
    }
    // Preparar la consulta con un marcador de posición (?)
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    // Vincular el parámetro (entero)
    $stmt->bind_param("i", $id);
    // Ejecutar la consulta
    $stmt->execute();
    // Obtener el resultado
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Nombre: " . $row["nombre"]. "<br>";
        }
    } else {
        echo "0 resultados";
    }
    $stmt->close();
}

// Vulnerabilidad de Cross-Site Scripting (XSS)

if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
