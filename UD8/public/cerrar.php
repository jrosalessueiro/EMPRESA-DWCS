<?php
// Iniciamos o recuperamos la sesión actual para poder manipularla
session_start();

// Eliminamos todas las variables de sesión (limpiamos el array $_SESSION)
session_unset();

// Destruimos la sesión por completo en el servidor para invalidar las cookies de sesión y asegurar que el usuario esté fuera
session_destroy();

// Redirigimos al usuario al login
header('Location: login.php');

// El exit() hace que el script se detenga y no gaste recursos innecesarios
exit();
