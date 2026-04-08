<?php
// Iniciamos la sesión para acceder a la "cesta" o cartera del usuario
session_start();

// Control de acceso: Si no existe la variable de sesión 'nombre' --> el usuario no ha pasado por el login 
// lo redirigimos al login.
if (!isset($_SESSION['nombre'])) {
    header('Location:login.php');
    exit();
}
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>StockMaster - Mi Cartera</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">

    <style>
        :root {
            --bg-dark: #020617;
            --card-dark: rgba(30, 41, 59, 0.8);
            --accent-blue: #38bdf8;
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
            margin: 0;
        }

        .glass-container,
        .card {
            background: var(--card-dark) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
            color: white !important;
        }

        .table {
            color: white !important;
        }

        .btn-info {
            background-color: var(--accent-blue) !important;
            border: none;
            color: #020617 !important;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-briefcase mr-2"></i> Mi Cartera de Inversión</h4>
                <a href="listado.php" class="btn btn-light btn-sm font-weight-bold">Volver al Buscador</a>
            </div>

            <div class="card-body">
                <?php
                /* Control de la cesta: verificamos si existe la cesta en la sesión o si está vacía */
                if (!isset($_SESSION['cesta']) || count($_SESSION['cesta']) == 0): ?>
                    <div class="text-center p-5">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <p class="lead">No tienes acciones en tu cartera todavía.</p>
                        <a href="listado.php" class="btn btn-primary">Buscar acciones ahora</a>
                    </div>
                <?php else: ?>
                    <table class="table table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>Símbolo de la Empresa</th>
                                <th>Precio de Adquisición</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0; // Acumulador para el valor total de la inversión

                            /* Recorremos el array asociativo de la cesta:
                               $simbolo es la clave que va a buscar en la API y $precio es el valor */
                            foreach ($_SESSION['cesta'] as $simbolo => $precio):
                                $total += $precio;
                            ?>
                                <tr>
                                    <td class="align-middle font-weight-bold"><?php echo $simbolo; ?></td>
                                    <td class="align-middle"><?php echo number_format($precio, 2); ?> $</td>
                                    <td class="text-center">
                                        <a href="detalles.php?id=<?php echo $simbolo; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Ver Detalles
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-success">
                                <td class="font-weight-bold h5">VALOR TOTAL DE LA CARTERA</td>
                                <td colspan="2" class="font-weight-bold h5 text-primary">
                                    <?php echo number_format($total, 2); ?> $
                                </td>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="vaciar.php" class="btn btn-outline-danger mr-2">Limpiar Cartera</a>
                        <a href="pagar.php" class="btn btn-primary btn-lg px-5">Ejecutar Inversión</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>