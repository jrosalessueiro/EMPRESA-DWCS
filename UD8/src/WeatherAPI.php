<?php

/**
 * Servicio meteorológico usando Open-Meteo.
 * No requiere API key y es totalmente gratuito.
 */
class WeatherAPI {

    public function getWeather($lat, $lon) {

        // Endpoint de Open-Meteo para obtener el tiempo actual
        $url = "https://api.open-meteo.com/v1/forecast?latitude=$lat&longitude=$lon&current_weather=true";

        // Inicializamos cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Ejecutamos la petición
        $response = curl_exec($ch);

        // Si hay error en cURL, lo devolvemos
        if ($response === false) {
            return ["error" => curl_error($ch)];
        }

        curl_close($ch);

        // Decodificamos JSON
        return json_decode($response, true);
    }
}
