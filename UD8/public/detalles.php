<?php
// Iniciamos sesión para verificar que el usuario está autenticado
session_start();
if (!isset($_SESSION['nombre'])) {
    header('Location:login.php');
    exit();
}

// Requerimos el autoload de Composer para cargar automáticamente nuestras clases
// El __DIR__ nos asegura que la ruta sea absoluta desde el directorio actual
require_once __DIR__ . '/../vendor/autoload.php';

// Importamos la clase del servicio mediante su Namespace
use jrosalessueiro\Tarea8\StockService;

// Instanciamos el servicio que conecta con la API externa
$service = new StockService();
$datos = null;

/**
 * CAPTURA DE DATOS POR GET: 
 * Recuperamos el 'id' enviado desde listado.php o cesta.php a través de la URL.
 * Usamos el operador de fusión de nulidad (??) para evitar errores si no existe.
 */
$simbolo = $_GET['id'] ?? null;

if ($simbolo) {
    // Limpiamos espacios en blanco y consultamos los datos financieros del símbolo
    $datos = $service->consultar(trim($simbolo));
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Detalles de <?php echo $id; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(rgba(2, 6, 23, 0.85), rgba(2, 6, 23, 0.92)),
                url('../img/Captura.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #f1f5f9;
            min-height: 100vh;
        }

        .card,
        .glass-container {
            background: rgba(30, 41, 59, 0.9) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 20px !important;
            color: white !important;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card glass-container shadow mx-auto" style="max-width: 600px;">
            <div class="card-header border-0 bg-transparent text-center">
                <h3 class="font-weight-bold text-white">DETALLE DEL ACTIVO</h3>
            </div>

            <div class="card-body">
                <?php
                /* RENDERIZADO:
                   Si el servicio devolvió datos válidos, pintamos la lista de detalles.
                   Usamos htmlspecialchars por seguridad para evitar ataques XSS al imprimir datos de la API. */
                if ($datos && isset($datos['simbolo'])): ?>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between border-secondary">
                            <strong>Símbolo:</strong>
                            <span class="text-info font-weight-bold"><?php echo htmlspecialchars($datos['simbolo']); ?></span>
                        </li>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between border-secondary">
                            <strong>Precio Actual:</strong>
                            <span class="font-weight-bold"><?php echo number_format($datos['precio'], 2); ?> $</span>
                        </li>
                        <li class="list-group-item bg-transparent text-white d-flex justify-content-between border-secondary">
                            <strong>Último Cambio:</strong>
                            <span class="<?php echo (strpos($datos['cambio'], '-') !== false) ? 'text-danger' : 'text-success'; ?>">
                                <?php echo htmlspecialchars($datos['cambio']); ?>
                            </span>
                        </li>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-search mb-3" style="font-size: 2rem; opacity: 0.5;"></i>
                        <p>No hay detalles disponibles para el símbolo: <strong><?php echo htmlspecialchars($simbolo); ?></strong></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-footer bg-transparent border-0 text-center">
                <small class="text-muted">Datos obtenidos via Alpha Vantage API</small>
                <br>
                <a href="listado.php" class="btn btn-outline-info mt-3 px-4">
                    <i class="fas fa-arrow-left"></i> Volver al Terminal
                </a>
            </div>
        </div>
    </div>
</body>

</html>