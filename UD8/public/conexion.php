<?php
// Configuración de la base de datos (XAMPP)
$host = "localhost";
$db   = "empresa_dwcs";
$user = "gestor";
$pass = "secreto";
$dsn  = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $conProyecto = new PDO($dsn, $user, $pass);
    $conProyecto->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    die("Error en la conexión: " . $ex->getMessage());
}

// Función auxiliar para cerrar la conexión
function cerrar(&$con)
{
    $con = null;
}
