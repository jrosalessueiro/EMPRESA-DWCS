<?php
session_start();
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
</head>

<body style="background: #e9ecef">
    <div class="container mt-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-briefcase mr-2"></i> Mi Cartera de Inversión</h4>
                <a href="listado.php" class="btn btn-light btn-sm font-weight-bold">Volver al Buscador</a>
            </div>
            <div class="card-body">
                <?php if (!isset($_SESSION['cesta']) || count($_SESSION['cesta']) == 0): ?>
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
                            $total = 0;
                            foreach ($_SESSION['cesta'] as $simbolo => $precio):
                                $total += $precio;
                            ?>
                                <tr>
                                    <td class="align-middle font-weight-bold"><?php echo $simbolo; ?></td>
                                    <td class="align-middle"><?php echo number_format($precio, 2); ?> $</td>
                                    <<td>
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
                                <td colspan="2" class="font-weight-bold h5 text-primary"><?php echo number_format($total, 2); ?> $</td>
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