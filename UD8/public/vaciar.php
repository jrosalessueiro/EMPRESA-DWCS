<?php
// public/vaciar.php
session_start();
unset($_SESSION['cesta']);
header('Location: cesta.php');
exit();
