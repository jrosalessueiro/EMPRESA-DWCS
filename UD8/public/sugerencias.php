<?php
// public/sugerencias.php
header('Content-Type: application/json');
require_once __DIR__ . '/../vendor/autoload.php';

use jrosalessueiro\Tarea8\StockService;

$service = new StockService();
$q = $_GET['q'] ?? '';

if (strlen($q) >= 2) {
    $url = "https://www.alphavantage.co/query?function=SYMBOL_SEARCH&keywords=" . urlencode($q) . "&apikey=TU_API_KEY";
    $json = @file_get_contents($url, false, stream_context_create(["ssl" => ["verify_peer" => false]]));
    $datos = json_decode($json, true);

    // Si la API responde con la nota de límite
    if (isset($datos['Note']) || isset($datos['Information'])) {
        // Enviamos un ítem especial para que el usuario vea el aviso en el desplegable
        echo json_encode([
            [
                '1. symbol' => 'AVISO',
                '2. name' => 'Límite de búsquedas API agotado por hoy',
                '4. region' => 'N/A'
            ]
        ]);
        exit;
    }

    echo json_encode($datos['bestMatches'] ?? []);
}
