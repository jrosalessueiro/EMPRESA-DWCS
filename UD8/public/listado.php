¡Para nada! No borres tu archivo actual, que ya tiene toda la estructura de Bootstrap y la lógica de sesión. Lo que vamos a hacer es insertar las nuevas funcionalidades dentro de lo que ya tienes.

He fusionado ambos fragmentos para que tu listado.php quede completo y con el buscador "inteligente".

public/listado.php (Código fusionado y completo)
Copia este código y sustituye todo el contenido de tu listado.php actual:

PHP
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
$error = "";

// 1. Lógica de búsqueda en la API REST
if (isset($_POST['enviar'])) {
    $simbolo = trim($_POST['simbolo']);
    if (!empty($simbolo)) {
        // Intentamos obtener el precio directamente
        $resultado = $service->consultar($simbolo);
        if (!$resultado) {
            $error = "No se encontraron datos exactos para: " . htmlspecialchars($simbolo) . ". Prueba a seleccionar una sugerencia del desplegable.";
        }
    }
}

// 2. Lógica para añadir a la cartera (Cesta)
if (isset($_POST['comprar'])) {
    $id = $_POST['id'];
    $precio = $_POST['precio'];
    $_SESSION['cesta'][$id] = $precio;
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
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h2>Buscador de Inversiones en Tiempo Real</h2>
                <p class="text-muted">Escribe el nombre de la empresa para ver sugerencias o el símbolo exacto.</p>
            </div>
        </div>

        <div class="card p-4 shadow-sm mb-4">
            <form action="" method="POST" class="form-inline justify-content-center">
                <input type="text" name="simbolo" list="opciones_bolsa" class="form-control form-control-lg mr-2 w-50"
                    placeholder="Ej: Apple, Microsoft, AAPL..." required
                    value="<?php echo isset($_POST['simbolo']) ? htmlspecialchars($_POST['simbolo']) : ''; ?>">

                <datalist id="opciones_bolsa">
                    <?php
                    // Si el usuario ha escrito algo, pedimos sugerencias a la API
                    if (isset($_POST['simbolo']) && !empty($_POST['simbolo'])) {
                        $sugerencias = $service->buscarSugerencias($_POST['simbolo']);
                        foreach ($sugerencias as $item) {
                            echo "<option value='" . $item['1. symbol'] . "'>" . $item['2. name'] . " (" . $item['4. region'] . ")</option>";
                        }
                    }
                    ?>
                </datalist>

                <button type="submit" name="enviar" class="btn btn-primary btn-lg">Consultar API</button>
                <a href="cesta.php" class="btn btn-success btn-lg ml-2"><i class="fas fa-wallet"></i> Mi Cartera</a>
            </form>
            <small class="text-muted text-center mt-2">Pista: Escribe un nombre, pulsa Consultar y luego elige el símbolo del desplegable.</small>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-warning text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($resultado): ?>
            <div class="row justify-content-center">
                <div class="card shadow-lg border-primary" style="width: 25rem;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title text-primary font-weight-bold"><?php echo $resultado['simbolo']; ?></h4>
                            <span class="badge badge-pill badge-info">Cotización Real</span>
                        </div>
                        <hr>
                        <p class="card-text display-4 text-center"><?php echo number_format($resultado['precio'], 2); ?> $</p>
                        <p class="text-center <?php echo (strpos($resultado['cambio'], '-') === false) ? 'text-success' : 'text-danger'; ?>">
                            Variación: <?php echo $resultado['cambio']; ?>
                        </p>
                        <form action="" method="POST">
                            <input type="hidden" name="id" value="<?php echo $resultado['simbolo']; ?>">
                            <input type="hidden" name="precio" value="<?php echo $resultado['precio']; ?>">
                            <button type="submit" name="comprar" class="btn btn-success btn-block btn-lg mt-3">
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