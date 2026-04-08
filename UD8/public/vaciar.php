<?php
session_start();
unset($_SESSION['cesta']);
header('Location: listado.php');
exit();
