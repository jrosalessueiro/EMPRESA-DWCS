<?php

namespace jrosalessueiro\Tarea8;

class StockService
{
    private $apiKey = "GVBVTZZ0F0SCL97G";
    private $baseUrl = "https://www.alphavantage.co/query";

    private function getJson($url)
    {
        $opts = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false]];
        $json = @file_get_contents($url, false, stream_context_create($opts));
        return $json ? json_decode($json, true) : null;
    }

    public function consultar($simbolo)
    {
        $url = "{$this->baseUrl}?function=GLOBAL_QUOTE&symbol={$simbolo}&apikey={$this->apiKey}";
        $datos = $this->getJson($url);

        if (isset($datos['Global Quote']['01. symbol'])) {
            $q = $datos['Global Quote'];
            return [
                'simbolo' => $q['01. symbol'],
                'precio'  => $q['05. price'],
                'cambio'  => $q['10. change percent']
            ];
        }
        return null;
    }

    public function buscarSugerencias($palabra)
    {
        $url = "{$this->baseUrl}?function=SYMBOL_SEARCH&keywords=" . urlencode($palabra) . "&apikey={$this->apiKey}";
        $datos = $this->getJson($url);
        return $datos['bestMatches'] ?? [];
    }
}
