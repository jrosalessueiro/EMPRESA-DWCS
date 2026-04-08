<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header('Location:login.php');
    exit();
}
// Vaciamos la cartera tras "pagar"
unset($_SESSION['cesta']);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Inversión Realizada</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="alert alert-success p-5 shadow">
            <h1 class="display-4 font-weight-bold">¡Operación de Bolsa Ejecutada!</h1>
            <p class="lead mt-3">Tus órdenes de compra han sido enviadas al mercado con éxito.</p>
            <hr>
            <a href="listado.php" class="btn btn-primary btn-lg mt-3">Volver al Dashboard</a>
        </div>
    </div>
</body>

</html>