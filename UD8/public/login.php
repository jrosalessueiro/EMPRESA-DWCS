<?php
session_start();
// 1. Cargamos la conexión y el autoload de clases
require_once 'conexion.php';
require_once __DIR__ . '/../vendor/autoload.php';

$error = "";

if (isset($_POST['login'])) {
    $usuario = trim($_POST['usuario']);
    $pass = $_POST['pass'];

    // --- LÓGICA DE VALIDACIÓN ---
    // Consultamos si el usuario existe en la tabla 'usuarios'
    $consulta = "SELECT pass FROM usuarios WHERE usuario = :u";
    $stmt = $conProyecto->prepare($consulta);

    try {
        $stmt->execute([':u' => $usuario]);
        $fila = $stmt->fetch(PDO::FETCH_OBJ);

        if ($fila) {

            if (password_verify($pass, $fila->pass) || $pass == $fila->pass) {
                $_SESSION['nombre'] = $usuario;
                header('Location: listado.php');
                exit();
            } else {
                $error = "Contraseña incorrecta.";
            }
        } else {
            $error = "El usuario no existe.";
        }
    } catch (PDOException $ex) {
        $error = "Error en la base de datos: " . $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>StockMaster - Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #343a40;
            color: white;
        }

        .card {
            color: black;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card mx-auto shadow-lg" style="max-width: 400px;">
            <div class="card-header bg-primary text-white text-center">
                <h4><i class="fas fa-chart-line"></i> StockMaster Login</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Usuario (gestor)</label>
                        <input type="text" name="usuario" class="form-control" placeholder="Introduce tu usuario" required>
                    </div>
                    <div class="form-group">
                        <label>Contraseña (secreto)</label>
                        <input type="password" name="pass" class="form-control" placeholder="Introduce tu clave" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary btn-block">Entrar al Panel</button>
                </form>
            </div>
            <div class="card-footer text-muted text-center">
                <small>DWCS - Tarea 08</small>
            </div>
        </div>
    </div>
</body>

</html>