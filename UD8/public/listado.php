<?php
// Iniciamos sesión para saber quién es el usuario y manejar su cesta
session_start();

// Control de seguridad: Si no se ha logueado, lo mandamos de vuelta al login
if (!isset($_SESSION['nombre'])) {
    header('Location:login.php');
    exit();
}

// Cargamos el Autoload de Composer para que reconozca las clases de nuestro Namespace
require_once __DIR__ . '/../vendor/autoload.php';

// Importamos la clase del servicio que se encarga de la lógica de negocio con la API
use jrosalessueiro\Tarea8\StockService;

// Instanciamos el servicio y preparamos variables para la vista
$service = new StockService();
$resultado = null;
$error = "";

/**
 * LÓGICA DE BÚSQUEDA:
 * El usuario puede llegar aquí por POST (formulario) o por GET (desde las sugerencias JS).
 * Usamos el operador ?? para priorizar el parámetro 'simbolo' del POST.
 */
$busqueda = $_POST['simbolo'] ?? $_GET['s'] ?? null;

if ($busqueda) {
    // Consultamos al servicio. El método 'consultar' encapsula la llamada a la API Alpha Vantage.
    $respuesta = $service->consultar(trim($busqueda));

    /* GESTIÓN DE ERRORES DEL SERVICIO:
       Controlamos si la API ha llegado al límite de llamadas gratuitas (rate limit) 
       o si simplemente no existen datos para ese ticker/símbolo. */
    if (isset($respuesta['tipo']) && $respuesta['tipo'] === 'limite') {
        $error = $respuesta['mensaje'];
        $resultado = null;
    } elseif (!$respuesta) {
        $error = "No se encontraron datos para este símbolo.";
        $resultado = null;
    } else {
        // Éxito: Guardamos la respuesta para pintarla luego en el HTML
        $resultado = $respuesta;
        $error = "";
    }
}

/**
 * LÓGICA DE COMPRA (Añadir a la Cartera):
 * Si se pulsa el botón 'comprar', guardamos el precio en el array de sesión 'cesta'.
 * Usamos el símbolo (id) como clave para evitar duplicados.
 */
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

    <style>
        :root {
            --bg-dark: #020617;
            --card-dark: rgba(30, 41, 59, 0.8);
            --accent-blue: #38bdf8;
            --stock-green: #22c55e;
        }

        body {
            background: linear-gradient(rgba(2, 6, 23, 0.85), rgba(2, 6, 23, 0.92)),
                url('../img/Captura.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }

        .glass-container {
            background: var(--card-dark);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        #menu-sugerencias {
            position: absolute;
            z-index: 1000;
            width: 100%;
            top: 100%;
            background: #1e293b;
            border: 1px solid var(--accent-blue);
            border-radius: 8px;
            display: none;
            overflow: hidden;
        }

        .sugerencia-item {
            padding: 12px;
            cursor: pointer;
            border-bottom: 1px solid #334155;
            transition: 0.2s;
        }

        .sugerencia-item:hover,
        .sugerencia-item.active {
            background: var(--accent-blue);
            color: #000;
            font-weight: bold;
        }

        .stock-card {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid var(--accent-blue);
            border-radius: 20px;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .price-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 4rem;
            color: var(--accent-blue);
        }
    </style>
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
                    <div class="alert text-center shadow-lg" style="background: rgba(239, 68, 68, 0.2); backdrop-filter: blur(8px); border: 1px solid #ef4444; color: #fca5a5; border-radius: 16px;">
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
                        // Lógica visual: Comprobamos si el cambio es negativo para colorear en rojo o verde
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

            // DEBOUNCE: Esperamos 500ms tras la última tecla para no saturar la API con peticiones
            timeout = setTimeout(() => {
                // Llamamos a un script intermedio 'sugerencias.php' que habla con la API
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
                                // Al hacer clic, redirigimos usando GET para cargar los datos
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

        // NAVEGACIÓN POR TECLADO: Permitimos seleccionar sugerencias con las flechas y Enter
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