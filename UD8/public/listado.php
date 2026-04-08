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

$busqueda = $_POST['simbolo'] ?? $_GET['s'] ?? null;

if ($busqueda) {
    $respuesta = $service->consultar(trim($busqueda));

    // Si el servicio devuelve que es un límite, NO asignamos nada a $resultado
    if (isset($respuesta['tipo']) && $respuesta['tipo'] === 'limite') {
        $error = $respuesta['mensaje'];
        $resultado = null;
    } elseif (!$respuesta) {
        $error = "No se encontraron datos para este símbolo.";
        $resultado = null;
    } else {
        // Solo si todo va bien, $resultado tiene los datos
        $resultado = $respuesta;
        $error = "";
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
    <title>StockMaster Pro | Terminal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">

</head>

<body>
    <nav class="navbar navbar-dark py-3">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="listado.php">
                <i class="fas fa-chart-line text-info"></i> STOCKMASTER <span class="badge badge-info ml-2">PRO</span>
            </a>
            <div class="ml-auto">
                <a href="cesta.php" class="btn btn-outline-info btn-sm mr-2"><i class="fas fa-wallet"></i> Cartera</a>
                <a href="cerrar.php" class="btn btn-outline-danger btn-sm"><i class="fas fa-power-off"></i></a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10 glass-container shadow">
                <h1 class="text-center font-weight-bold mb-4">Terminal Financiera</h1>

                <form action="listado.php" method="POST" class="form-inline justify-content-center mb-0">
                    <div class="position-relative" style="width: 75%;">
                        <input type="text" id="buscador" name="simbolo" class="form-control form-control-lg w-100"
                            placeholder="Empresa o Símbolo (ej: Microsoft...)" autocomplete="off" required>
                        <div id="menu-sugerencias" class="shadow-lg"></div>
                    </div>
                    <button type="submit" class="btn btn-info btn-lg ml-3 px-4 shadow">CONSULTAR</button>
                </form>
            </div>

            <?php if ($error): ?>
                <div class="col-md-10 mt-4 animate__animated animate__headShake">
                    <div class="alert text-center shadow-lg"
                        style="background: rgba(239, 68, 68, 0.2); 
                    backdrop-filter: blur(8px); 
                    border: 1px solid #ef4444; 
                    color: #fca5a5; 
                    border-radius: 16px;">
                        <i class="fas fa-exclamation-circle mb-2" style="font-size: 1.5rem;"></i>
                        <h5 class="font-weight-bold">AVISO DEL SISTEMA</h5>
                        <p class="mb-1"><?php echo $error; ?></p>
                        <small class="opacity-75">Las limitaciones de la API gratuita pueden restringir los datos en tiempo real.</small>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($resultado && isset($resultado['simbolo'])): ?>
                <div class="col-md-6 mt-5">
                    <div class="stock-card p-4 text-center">
                        <h6 class="text-muted small">SYMBOL</h6>
                        <h2 class="font-weight-bold"><?php echo htmlspecialchars($resultado['simbolo']); ?></h2>

                        <hr style="border-color: #334155">

                        <div class="price-value my-3">
                            <?php echo number_format($resultado['precio'] ?? 0, 2); ?>$
                        </div>

                        <?php
                        $cambio = $resultado['cambio'] ?? '0%';
                        $esNegativo = (strpos($cambio, '-') !== false);
                        ?>
                        <div class="h4 <?php echo $esNegativo ? 'text-danger' : 'text-success'; ?>">
                            <?php echo htmlspecialchars($cambio); ?>
                        </div>

                        <form method="POST" class="mt-4">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($resultado['simbolo']); ?>">
                            <input type="hidden" name="precio" value="<?php echo htmlspecialchars($resultado['precio']); ?>">
                            <button type="submit" name="comprar" class="btn btn-success btn-block btn-lg shadow-sm">
                                <i class="fas fa-plus"></i> Añadir al Portafolio
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const buscador = document.getElementById('buscador');
        const menu = document.getElementById('menu-sugerencias');
        let indexSeleccionado = -1;
        let timeout = null;

        buscador.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();
            if (query.length < 2) {
                menu.style.display = 'none';
                return;
            }

            // Debounce de 500ms para no agotar la API
            timeout = setTimeout(() => {
                fetch(`sugerencias.php?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        menu.innerHTML = '';
                        indexSeleccionado = -1;
                        if (data && data.length > 0) {
                            data.forEach((item) => {
                                const div = document.createElement('div');
                                div.className = 'sugerencia-item';
                                div.innerHTML = `<strong>${item['1. symbol']}</strong> - ${item['2. name']}`;
                                div.onclick = () => {
                                    window.location.href = `listado.php?s=${item['1. symbol']}`;
                                };
                                menu.appendChild(div);
                            });
                            menu.style.display = 'block';
                        }
                    });
            }, 500);
        });

        buscador.addEventListener('keydown', function(e) {
            const items = menu.getElementsByClassName('sugerencia-item');
            if (menu.style.display === 'block') {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    indexSeleccionado = (indexSeleccionado + 1) % items.length;
                    actualizarFoco(items);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    indexSeleccionado = (indexSeleccionado - 1 + items.length) % items.length;
                    actualizarFoco(items);
                } else if (e.key === 'Enter' && indexSeleccionado > -1) {
                    e.preventDefault();
                    window.location.href = `listado.php?s=${items[indexSeleccionado].querySelector('strong').innerText}`;
                }
            }
        });

        function actualizarFoco(items) {
            Array.from(items).forEach(i => i.classList.remove('active'));
            if (indexSeleccionado > -1) items[indexSeleccionado].classList.add('active');
        }
    </script>
</body>

</html>