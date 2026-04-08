<?php
session_start();
if (!isset($_SESSION['nombre']) || !isset($_GET['id'])) {
    header('Location:listado.php');
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';

use jrosalessueiro\Tarea8\StockService;

$id = $_GET['id'];
$service = new StockService();
$info = $service->consultar($id);
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalles de <?php echo $id; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow mx-auto" style="max-width: 500px;">
            <div class="card-header bg-info text-white text-center">
                <h3>Detalles de Activo: <?php echo $id; ?></h3>
            </div>
            <div class="card-body">
                <?php if ($info): ?>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Símbolo:</strong> <?php echo $info['simbolo']; ?></li>
                        <li class="list-group-item"><strong>Precio Actual:</strong> <?php echo number_format($info['precio'], 2); ?> $</li>
                        <li class="list-group-item"><strong>Variación:</strong> <?php echo $info['cambio']; ?></li>
                        <li class="list-group-item text-muted small text-right">Datos obtenidos via Alpha Vantage API</li>
                    </ul>
                <?php else: ?>
                    <p class="text-danger">No se pudo recuperar la información en este momento.</p>
                <?php endif; ?>
                <div class="mt-4">
                    <a href="cesta.php" class="btn btn-secondary btn-block">Volver a mi Cartera</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>