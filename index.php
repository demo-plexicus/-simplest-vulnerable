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

/*
Pre-implementation analysis:
1) Vulnerabilidad a corregir: SQL Injection en la construcción de la consulta que incorpora directamente input no confiable ($_GET['id']).
2) Cambios: validar que 'id' sea solo dígitos (whitelist) y usar una sentencia preparada (prepared statement) con binding de parámetros para evitar inyección.
3) Archivo modificado: index.php (este bloque reemplaza el manejo vulnerable).
*/
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Validar que el id sea un entero (solo dígitos) para implementar una política de whitelist
    if (!ctype_digit($id)) {
        echo "ID inválido";
    } else {
        // Usar sentencia preparada para evitar SQL Injection
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
        if ($stmt) {
            // Bind como entero; $id contiene solo dígitos por la validación anterior
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Obtener resultados de la sentencia preparada
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "id: " . $row["id"]. " - Nombre: " . $row["nombre"]. "<br>";
                }
            } else {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // En caso de error al preparar la sentencia, informar de forma genérica
            echo "Error en la consulta";
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
