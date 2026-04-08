<?php

/**
 * 
 * Partes de este archivo las he copiado de Stack Overflow 
 * 
 * 
 * 
 * CABECERA DE CONTENIDO:
 * Indicamos al navegador que este archivo no devuelve HTML, sino un JSON --> es necesarios para que el fetch() de JS lo procese correctamente.
 */
header('Content-Type: application/json');

// Cargamos las dependencias de Composer para tener acceso a nuestras clases si fuera necesario
require_once __DIR__ . '/../vendor/autoload.php';

use jrosalessueiro\Tarea8\StockService;

$service = new StockService();

/**
 * CAPTURA DE LA CONSULTA (ENTRY POINT):
 * * 1. ORIGEN (CLIENT-SIDE): Este script es invocado de forma asíncrona mediante AJAX (fetch) desde el frontend. 
 * El parámetro 'q' está en la Query String de la URL (ej: sugerencias.php?q=AAPL).
 * * 2. SUPERGLOBAL $_GET: PHP captura automáticamente cualquier dato de la URL y lo 
 * almacena en el array asociativo $_GET, usando como clave el nombre del parámetro.
 * * 3. OPERADOR NULL COALESCE (??): Usamos este operador para dar robustez al script. 
 * Si intentamos acceder a $_GET['q'] y el parámetro no ha sido enviado, PHP daría un error de tipo 'Undefined index'. 
 * - Si $_GET['q'] existe, se asigna su valor a $q.
 * - Si no existe (es null), se asigna un string vacío ('') por defecto.
 * * 4. SANITIZACIÓN: Posteriormente, el valor se procesará para evitar problemas en la petición a la API externa.
 */
$q = $_GET['q'] ?? '';

// Solo realizamos la petición si el usuario ha escrito al menos 2 caracteres (optimización de red)
if (strlen($q) >= 2) {

    /**
     * API EXTERNA:
     * 1. CONSTRUCCIÓN DE LA URI: Concatenamos los parámetros requeridos por Alpha Vantage. 
     * Aplicamos urlencode() a la búsqueda para asegurar que caracteres especiales o espacios no rompan la URL
     * * 2. PETICIÓN ASÍNCRONA DE SERVIDOR A SERVIDOR: 
     * Usamos file_get_contents para realizar una petición GET. Configuramos un 'stream context' 
     * para deshabilitar la verificación de pares SSL (verify_peer => false), evitando errores habituales en local.
     * * 3. DESERIALIZACIÓN DE DATOS: 
     * La API devuelve un flujo de datos en formato JSON (texto plano). Utilizamos json_decode 
     * con el flag 'true' para transformar esa cadena en un array asociativo de PHP, 
     */
    $url = "https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords=" . urlencode($q) . "&apikey=TU_API_KEY";

    $json = @file_get_contents($url, false, stream_context_create(["ssl" => ["verify_peer" => false]]));

    $datos = json_decode($json, true);

    /**
     * CONTROL DE RATE LIMIT (Límite de la API):
     * Las APIs gratuitas Alpha Vantage tiene el límite de 5 peticiones por minuto.
     * Si la superamos nos bloquea temporalmente y nos envía una 'Note' o 'Information'.
     */
    if (isset($datos['Note']) || isset($datos['Information'])) {
        /* Cuando falla enviamos un mensaje estructurado que el JS del buscador pueda mostrar al usuario. */
        echo json_encode([
            [
                '1. symbol' => 'AVISO',
                '2. name' => 'Límite de búsquedas API agotado por hoy',
                '4. region' => 'N/A'
            ]
        ]);
        exit;
    }

    /**
     * RESPUESTA FINAL:
     * Enviamos de vuelta solo el array 'bestMatches', que contiene las empresas encontradas.
     * Si no hay resultados, devolvemos un array vacío [].
     */
    echo json_encode($datos['bestMatches'] ?? []);
}
