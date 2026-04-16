<?php
// Parámetros de configuración de la base de datos (Entorno local XAMPP)
$host = "localhost";
$db   = "empresa_dwcs";
$user = "gestor";
$pass = "secreto";

// DSN (Data Source Name): Cadena de conexión que indica el driver, host, base de datos y el set de caracteres
// Usamos utf8mb4 para asegurar que emojis o caracteres especiales no den problemas
$dsn = "mysql:host=localhost;port=3307;dbname=tarea06;charset=utf8mb4";

try {
    // Creamos la instancia de la clase PDO para establecer la conexión activa.
    $conProyecto = new PDO($dsn, $user, $pass);

    /* Manejo de errores:
       ATTR_ERRMODE => ERRMODE_EXCEPTION le dice a PDO que lance excepciones cuando algo falle.
       Esto nos permite capturarlas en el bloque catch y evitar que se filtren datos sensibles como rutas o usuarios. */
    $conProyecto->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    // Si la conexión falla, detenemos la ejecución con el die y mostramos el error.
    die("Error en la conexión: " . $ex->getMessage());
}

/**
 * Función auxiliar para cerrar la conexión de forma limpia.
 * Al pasar la conexión por referencia (&$con) y ponerla a null,liberamos el objeto de la memoria del servidor.
 */
function cerrar(&$con)
{
    $con = null;
}
