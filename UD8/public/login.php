<?php
// Iniciamos la sesión al principio para poder guardar el nombre del usuario si el login es correcto
session_start();

// Incluimos el script de conexión que usa PDO
require_once 'conexion.php';

$error = "";

/**
 * PROCESAMIENTO DEL FORMULARIO:
 * Verificamos si el usuario ha pulsado el botón de enviar (método POST).
 */
if (isset($_POST['login'])) {
    $usuario = trim($_POST['usuario']);
    $password = $_POST['password'];

    // Validación básica: comprobamos que los campos no lleguen vacíos
    if (empty($usuario) || empty($password)) {
        $error = "Por favor, rellena todos los campos.";
    } else {
        try {
            /* *Si concatenamos variables directas en el SQL la app no seria segura.
             * * 2. La solución: 
             * - Paso A ($sql): Creamos una "plantilla" con un marcador (:u). Es un hueco vacío.
             * - Paso B (prepare): Enviamos la plantilla a la BD. La BD ya sabe qué "forma" tiene la orden.
             * - Paso C (execute): Enviamos el dato real ($usuario). PDO lo limpia de cualquier código 
             *  antes de meterlo en el hueco (:u). 
             * * 3. El resultado ($row): Obtenemos los datos de forma segura.. */

            // Definimos la plantilla con el marcador :u
            $sql = "SELECT nombre, password FROM usuarios WHERE usuario = :u";
            // Preparamos la consulta en el servidor de BD
            $stmt = $conProyecto->prepare($sql);
            // Ejecutamos pasando el valor real que limpia el marcador
            $stmt->execute([':u' => $usuario]);
            // Recuperamos la fila como un array asociativo (clave => valor)
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            /**
             * VERIFICACIÓN DE CREDENCIALES:
             * Comprobamos si el usuario existe y si la contraseña coincide.
             * Si usasemos password_hash() en el registro, aquí usaríamos password_verify().
             */
            if ($row && $password === $row['password']) {
                // ÉXITO: Guardamos datos en la sesión para identificar al usuario en otras páginas
                $_SESSION['nombre'] = $row['nombre'];
                $_SESSION['cesta'] = []; // Inicializamos la cartera vacía para el nuevo usuario

                // Redirigimos al terminal principal
                header('Location: listado.php');
                exit();
            } else {
                $error = "Usuario o contraseña incorrectos.";
            }
        } catch (PDOException $ex) {
            $error = "Error en el sistema: " . $ex->getMessage();
        } finally {
            // Cerramos la conexión usando la función auxiliar de conexion.php
            cerrar($conProyecto);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>StockMaster Pro | Acceso</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(rgba(2, 6, 23, 0.8), rgba(2, 6, 23, 0.9)),
                url('../img/Captura.jpg') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card shadow-lg p-4">
                    <div class="text-center mb-4">
                        <h2 class="font-weight-bold text-info">STOCKMASTER</h2>
                        <p class="text-muted">Inicia sesión para operar</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 text-center small"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label>Usuario</label>
                            <input type="text" name="usuario" class="form-control bg-dark border-secondary text-white" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña</label>
                            <input type="password" name="password" class="form-control bg-dark border-secondary text-white" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-info btn-block font-weight-bold mt-4">ACCEDER AL TERMINAL</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>