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

// Vulnerabilidad de SQL Injection
// El siguiente código es vulnerable a SQL Injection ya que el input del usuario se concatena directamente en la consulta SQL sin validación o sanitización.
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Cesar PwC
    // Remediación breve: Validar que 'id' sea numérico y usar consultas preparadas (prepared statements)
    // para evitar inyección SQL. Se emplea una validación whitelist (solo dígitos) y binding de parámetros.
    if (!preg_match('/^\d+$/', $id)) {
        echo "ID inválido";
        $result = null;
    } else {
        $id_int = (int)$id;
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id_int);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            // Si la preparación falla, evita ejecutar consultas concatenadas con input del usuario.
            echo "Error en la consulta";
            $result = null;
        }
    }

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"]. " - Nombre: " . $row["nombre"]. "<br>";
        }

        echo "0 resultados";
    }
}

// Vulnerabilidad de Cross-Site Scripting (XSS)
// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    echo "<div>$mensaje</div>"; // Vulnerable a XSS
}

// Cerrar conexión
$conn->close();
?>
