<?php
session_start();
if (!isset($_SESSION['nombre'])) {
    header('Location:login.php');
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';

use jrosalessueiro\Tarea8\StockService;

$service = new StockService();
$resultado = null;
$sugerencias = [];
$error = "";

if (isset($_POST['enviar']) || isset($_GET['s'])) {
    $simbolo = isset($_POST['simbolo']) ? trim($_POST['simbolo']) : trim($_GET['s']);

    if (!empty($simbolo)) {
        $resultado = $service->consultar($simbolo);
        if (!$resultado) {
            // Si falla el precio, buscamos nombres parecidos
            $sugerencias = $service->buscarSugerencias($simbolo);
            if (empty($sugerencias)) {
                $error = "No se encontró nada para '$simbolo'.";
            }
        }
    }
}

if (isset($_POST['comprar'])) {
    $_SESSION['cesta'][$_POST['id']] = $_POST['precio'];
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>StockMaster - Buscador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Buscador Inteligente StockMaster</h2>

        <div class="card p-4 shadow-sm">
            <form method="POST" class="form-inline justify-content-center">
                <input type="text" name="simbolo" class="form-control mr-2 w-50" placeholder="Escribe Apple o AAPL..." required>
                <button type="submit" name="enviar" class="btn btn-primary">Buscar</button>
                <a href="cesta.php" class="btn btn-success ml-2">Cartera</a>
            </form>
        </div>

        <?php if (!empty($sugerencias)): ?>
            <div class="mt-4">
                <p class="text-center">¿Te refieres a una de estas empresas? Haz clic para ver precio:</p>
                <div class="list-group mx-auto" style="max-width: 500px;">
                    <?php foreach ($sugerencias as $s): ?>
                        <a href="listado.php?s=<?php echo $s['1. symbol']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between">
                            <span><strong><?php echo $s['1. symbol']; ?></strong> - <?php echo $s['2. name']; ?></span>
                            <span class="badge badge-primary badge-pill">Consultar</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($resultado): ?>
            <div class="card mt-4 mx-auto shadow border-primary" style="max-width: 400px;">
                <div class="card-body text-center">
                    <h4 class="text-primary"><?php echo $resultado['simbolo']; ?></h4>
                    <h2 class="display-4"><?php echo number_format($resultado['precio'], 2); ?> $</h2>
                    <p class="text-muted">Variación: <?php echo $resultado['cambio']; ?></p>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $resultado['simbolo']; ?>">
                        <input type="hidden" name="precio" value="<?php echo $resultado['precio']; ?>">
                        <button type="submit" name="comprar" class="btn btn-success btn-block">Añadir a Cartera</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error && empty($sugerencias)): ?>
            <div class="alert alert-danger mt-3 text-center"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>

</html>