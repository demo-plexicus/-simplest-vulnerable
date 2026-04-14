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

// Vulnerabilidad de SQL Injection - FIXED
// Se ha implementado una consulta preparada para prevenir SQL Injection
if(isset($_GET['id'])) {
    // Validar que el id sea numérico (opcional, pero recomendado)
    if (!is_numeric($_GET['id'])) {
        echo "Error: ID debe ser numérico";
        exit;
    }
    
    $id = (int)$_GET['id']; // Convertir a entero para mayor seguridad
    
    // Usar consulta preparada para prevenir SQL Injection
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id); // "i" indica que el parámetro es un entero
    $stmt->execute();
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
