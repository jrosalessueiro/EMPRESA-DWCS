<?php

namespace jrosalessueiro\Tarea8;

class StockService
{
    private $apiKey = "GVBVTZZ0F0SCL97G";
    private $baseUrl = "https://www.alphavantage.co/query";

    public function consultar($simbolo)
    {
        $simbolo = urlencode(strtoupper($simbolo));
        $url = "{$this->baseUrl}?function=GLOBAL_QUOTE&symbol={$simbolo}&apikey={$this->apiKey}";

        // Usamos una opción para ignorar el SSL también en la petición PHP por si acaso me sigue dando error de seguridad
        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];

        $json = @file_get_contents($url, false, stream_context_create($arrContextOptions));
        if (!$json) return null;

        $datos = json_decode($json, true);

        if (isset($datos['Global Quote']) && !empty($datos['Global Quote'])) {
            $q = $datos['Global Quote'];
            return [
                'simbolo' => $q['01. symbol'],
                'precio'  => (float)$q['05. price'],
                'cambio'  => $q['10. change percent']
            ];
        }
        return null;
    }

    public function buscarSugerencias($palabra)
    {
        $palabra = urlencode($palabra);
        $url = "{$this->baseUrl}?function=SYMBOL_SEARCH&keywords={$palabra}&apikey={$this->apiKey}";

        $json = @file_get_contents($url, false, stream_context_create([
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ]));

        $datos = json_decode($json, true);
        return $datos['bestMatches'] ?? [];
    }
}
