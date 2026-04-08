<?php

/**
 * Iniciamos o reanudamos la sesión existente para poder acceder al array global $_SESSION.
 */
session_start();

/**
 * Utilizamos unset() para eliminar específicamente la clave 'cesta'.
 */
unset($_SESSION['cesta']);

/**
 * Redirigimos al usuario de vuelta a la página de la cesta ( ahora vacía). 
 * Usamos exit() inmediatamente después para asegurar que el script se detanga.
 */
header('Location: cesta.php');
exit();
