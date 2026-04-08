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

// Lógica de búsqueda (Acepta POST del formulario o GET de las sugerencias)
$busqueda = $_POST['simbolo'] ?? $_GET['s'] ?? null;

if ($busqueda) {
    $simbolo = strtoupper(trim($busqueda));
    $resultado = $service->consultar($simbolo);

    if (!$resultado) {
        $sugerencias = $service->buscarSugerencias($simbolo);
        if (empty($sugerencias)) {
            $error = "No se encontraron datos para: " . htmlspecialchars($simbolo);
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
</head>

<body style="background: #f4f7f6">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <span class="navbar-brand mb-0 h1"><i class="fas fa-chart-line text-success"></i> StockMaster</span>
        <div class="text-white">
            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['nombre']; ?>
            <a href="cerrar.php" class="btn btn-outline-danger btn-sm ml-3">Salir</a>
        </div>
    </nav>

    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-md-12">
                <h2>Buscador de Inversiones en Tiempo Real</h2>
                <p class="text-muted">Introduce el nombre de la empresa o su símbolo bursátil</p>
            </div>
        </div>

        <div class="card p-4 shadow-sm mb-4">
            <form action="listado.php" method="POST" class="form-inline justify-content-center">
                <input type="text" name="simbolo" class="form-control form-control-lg mr-2 w-50"
                    placeholder="Ej: Apple o AAPL" required>
                <button type="submit" class="btn btn-primary btn-lg">Consultar API</button>
                <a href="cesta.php" class="btn btn-success btn-lg ml-2"><i class="fas fa-wallet"></i> Mi Cartera</a>
            </form>
        </div>

        <?php if (!empty($sugerencias)): ?>
            <div class="alert alert-info shadow-sm">
                <h5><i class="fas fa-info-circle"></i> No encontramos "<?php echo htmlspecialchars($busqueda); ?>". ¿Te refieres a alguna de estas?</h5>
                <div class="row mt-3">
                    <?php foreach ($sugerencias as $s): ?>
                        <div class="col-md-4 mb-2">
                            <a href="listado.php?s=<?php echo $s['1. symbol']; ?>" class="btn btn-outline-primary btn-block text-left">
                                <strong><?php echo $s['1. symbol']; ?></strong><br>
                                <small><?php echo substr($s['2. name'], 0, 30); ?>...</small>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error && empty($sugerencias)): ?>
            <div class="alert alert-warning text-center shadow-sm"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($resultado): ?>
            <div class="row justify-content-center">
                <div class="card shadow-lg border-primary" style="width: 25rem;">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title text-primary font-weight-bold mb-0"><?php echo $resultado['simbolo']; ?></h4>
                            <span class="badge badge-pill badge-info">Cotización Real</span>
                        </div>
                        <hr>
                        <p class="card-text display-4"><?php echo number_format($resultado['precio'], 2); ?> $</p>
                        <p class="h5 <?php echo (strpos($resultado['cambio'], '-') === false) ? 'text-success' : 'text-danger'; ?>">
                            <i class="fas <?php echo (strpos($resultado['cambio'], '-') === false) ? 'fa-caret-up' : 'fa-caret-down'; ?>"></i>
                            <?php echo $resultado['cambio']; ?>
                        </p>
                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?php echo $resultado['simbolo']; ?>">
                            <input type="hidden" name="precio" value="<?php echo $resultado['precio']; ?>">
                            <button type="submit" name="comprar" class="btn btn-success btn-block btn-lg mt-4">
                                <i class="fas fa-plus-circle"></i> Añadir a mi Cartera
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>