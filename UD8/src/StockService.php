<?php

//La parte de acceso a la API Alpha Vantage las copié de Stack Overflow

/**
 * DEFINICIÓN DEL NAMESPACE: para indicar que esta clase es mía.
 */

namespace jrosalessueiro\Tarea8;

/**
 * CLASE StockService:
 * Esta clase centraliza todas las llamadas a la API externa. 
 * Encapsulamos para no mostrar la URL de la API ni la Key, solo llama a los métodos de esta clase.
 */
class StockService
{
    // Atributos privados: Solo esta clase puede ver la Key y la URL base (Seguridad)
    private $apiKey = "GVBVTZZ0F0SCL97G";
    private $baseUrl = "https://www.alphavantage.co/query";

    /**
     * MÉTODO consultar($simbolo):
     * Obtiene el precio real de una acción específica.
     */
    public function consultar($simbolo)
    {
        // Construimos la URL usando la función GLOBAL_QUOTE de la API
        $url = "{$this->baseUrl}?function=GLOBAL_QUOTE&symbol=" . urlencode($simbolo) . "&apikey={$this->apiKey}";

        // Realizamos la petición HTTP ignorando errores de SSL para entornos locales
        $json = @file_get_contents($url, false, stream_context_create([
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ]));

        // Decodificamos el JSON a un array asociativo
        $datos = json_decode($json, true);

        /**
         * LÓGICA DE RESPUESTA según el caso:
         * CASO 1: Éxito. La API nos devuelve los datos financieros.
         * Mapeamos las claves raras de la API (01, 05...) a nombres legibles.
         */
        if (isset($datos['Global Quote']['01. symbol'])) {
            $q = $datos['Global Quote'];
            return [
                'tipo' => 'exito',
                'simbolo' => $q['01. symbol'],
                'precio'  => $q['05. price'],
                'cambio'  => $q['10. change percent']
            ];
        }

        /**
         * CASO 2: Rate Limit. La API gratuita tiene límites (5 consultas/minuto y 25 consultas/día).
         * Detectamos el aviso y retornamos un tipo 'limite' para avisar al usuario.
         */
        if (isset($datos['Note']) || isset($datos['Information'])) {
            return [
                'tipo' => 'limite',
                'mensaje' => "Límite de la API alcanzado (25/día). Inténtalo más tarde."
            ];
        }

        // CASO 3: Error o Símbolo no encontrado. Retornamos null.
        return null;
    }

    /**
     * MÉTODO buscarSugerencias($palabra):
     * Utiliza la función SYMBOL_SEARCH para el buscador predictivo.
     * @return array Lista de coincidencias encontradas.
     */
    public function buscarSugerencias($palabra)
    {
        $url = "{$this->baseUrl}?function=SYMBOL_SEARCH&keywords=" . urlencode($palabra) . "&apikey={$this->apiKey}";

        $json = @file_get_contents($url, false, stream_context_create([
            "ssl" => ["verify_peer" => false, "verify_peer_name" => false]
        ]));

        $datos = json_decode($json, true);

        // Retornamos el array de coincidencias o un array vacío si no hay nada
        return $datos['bestMatches'] ?? [];
    }
}
