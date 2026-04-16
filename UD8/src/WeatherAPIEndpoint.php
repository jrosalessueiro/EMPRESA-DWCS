
<?php
require_once "WeatherAPI.php";

header("Content-Type: application/json");

$api = new WeatherAPI();

// Coordenadas de Pontevedra (puedes cambiarlas por las de tu ciudad)
$lat = 42.4333;
$lon = -8.6333;

echo json_encode($api->getWeather($lat, $lon));
 /*
 /** 
 * Mi API no devuelve JSON directamente, sino un array PHP.
 * Así que necesitamos un pequeño archivo puente para convertirlo en JSON.
 * este es el archivo puente: WeatherAPIEndpoint.php
 * Este archivo se encarga de recibir la petición, llamar a la clase WeatherAPI y devolver el resultado en formato JSON.
 * Endpoint para obtener el clima usando la API de Open-Meteo.
 */