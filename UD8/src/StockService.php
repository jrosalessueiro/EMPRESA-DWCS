<?php

namespace jrosalessueiro\Tarea8;

class StockService
{
    private $apiKey = "GVBVTZZ0F0SCL97G";
    private $baseUrl = "https://www.alphavantage.co/query";

    public function consultar($simbolo)
    {
        $url = "{$this->baseUrl}?function=GLOBAL_QUOTE&symbol=" . urlencode($simbolo) . "&apikey={$this->apiKey}";

        $json = @file_get_contents($url, false, stream_context_create([
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ]));

        $datos = json_decode($json, true);

        // CASO 1: Éxito (devuelve el precio)
        if (isset($datos['Global Quote']['01. symbol'])) {
            $q = $datos['Global Quote'];
            return [
                'tipo' => 'exito',
                'simbolo' => $q['01. symbol'],
                'precio'  => $q['05. price'],
                'cambio'  => $q['10. change percent']
            ];
        }

        // CASO 2: Límite de la API (devuelve un mensaje de aviso)
        if (isset($datos['Note']) || isset($datos['Information'])) {
            return [
                'tipo' => 'limite',
                'mensaje' => "Límite de la API alcanzado (25/día). Inténtalo más tarde."
            ];
        }

        // CASO 3: Símbolo no existe
        return null;
    }

    public function buscarSugerencias($palabra)
    {
        $url = "{$this->baseUrl}?function=SYMBOL_SEARCH&keywords=" . urlencode($palabra) . "&apikey={$this->apiKey}";
        // Importante: No uses die() ni echo aquí, solo retorna el array
        $json = @file_get_contents($url, false, stream_context_create([
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ]));
        $datos = json_decode($json, true);
        return $datos['bestMatches'] ?? [];
    }
}
