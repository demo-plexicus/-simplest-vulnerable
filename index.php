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
/*
 Changelog (index.php):
 - 2026-02-25: Fixed SQL Injection and output encoding.
   * Replaced string-concatenated SQL with a prepared statement and integer validation.
   * Ensured database-derived fields are HTML-encoded before output to mitigate XSS.
 Analysis:
 - Vulnerability fixed: SQL Injection in the id-based query and reflected XSS when printing user/database content.
 - Changes made: validate/cast id to integer; use mysqli prepared statements with a bound parameter; use htmlspecialchars()
   to encode all user-supplied or database-derived strings before emitting into HTML.
 Note: To lint and commit after applying this change run: php -l index.php  and then git add index.php && git commit -m "Fix SQLi/XSS in index.php"
*/
if(isset($_GET['id'])) {
    $id = $_GET['id']; // Input del usuario tomado directamente desde la URL

    // Validate that id is an integer to avoid misuse (defensive)
    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        // Invalid id -> report no results without revealing internal details
        echo "0 resultados";
    } else {
        $id = (int)$id;

        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Bind result columns and fetch safely
            $stmt->bind_result($res_id, $res_nombre);
            $hasRows = false;
            while ($stmt->fetch()) {
                $hasRows = true;
                // Encode all output for HTML context to prevent XSS
                echo "id: " . htmlspecialchars($res_id, ENT_QUOTES, 'UTF-8') . " - Nombre: " . htmlspecialchars($res_nombre, ENT_QUOTES, 'UTF-8') . "<br>";
            }

            if (!$hasRows) {
                echo "0 resultados";
            }

            $stmt->close();
        } else {
            // Prepare failed (possible DB issue) -> do not leak details
            echo "0 resultados";
        }
    }
}

// Vulnerabilidad de Cross-Site Scripting (XSS)
// El siguiente código es vulnerable a XSS ya que imprime directamente en el HTML el contenido de una variable que puede ser manipulada por el usuario sin ninguna sanitización.
if(isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje']; // Input del usuario susceptible a XSS
    // Encode user-controlled content for HTML context to mitigate reflected XSS
    echo "<div>" . htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . "</div>";
}

// Cerrar conexión

?>
