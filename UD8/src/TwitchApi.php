<?php

/*
Clase encargada de gestionar tokens de acceso, obtención de id del jeugo seleccionado
    y de la optención de clips
*/
class TwitchApi{
    private $clientID;
    private $clientSecret;
    private $accessToken;

    public function __construct($clientID, $clientSecret){
        $this->clientID = $clientID;
        $this->clientSecret = $clientSecret;
    }

    //obtención de tokens de acceso
    public function getAccessToken(){
        $url = "https://id.twitch.tv/oauth2/token";

        $params = [
            'client_id' => $this->clientID,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials'
        ];

        //https://www.php.net/manual/es/function.curl-setopt.php
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true); //indicamos que se usa metodo POST para enviar info
        
        /*
        con 'http_build_query' tomamos el array asociativo $params y lo pasamos a texto
        luego con 'CURLOPT_POSTFIELDS' recibe la cadena y la coloca en la peticion
        */
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $resp = curl_exec($ch);
        //convertimos el JSON en un array asociativo
        $data = json_decode($resp, true);
        curl_close($ch);

        if(isset($data['access_token'])){
            $this->accessToken = $data['access_token'];
            return $this->accessToken;
        }

        return null;
    }

    //buscamos el ID de la categoría de nuestro interés, en este caso 'crypto'
    public function getCategoryId() {

        $url = "https://api.twitch.tv/helix/games?name=" . urlencode("Crypto");

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->accessToken,
            "Client-Id: " . $this->clientID
        ]);

        $resp = curl_exec($ch);
        $data = json_decode($resp, true);
        curl_close($ch);

        //verificamos si la api devuelve resultado. Si es true devuelve el id
        if (isset($data['data'][0]['id'])) {
            return $data['data'][0]['id'];
        }

        return null;
    }


    //buscamos N canales que pertenezcan a la categoría.
    //Se filtra/detecta automáticamente que estén online
    public function getCanales($id){
        
    /*
    por defecto los clips vienen ordenados por valor decreciente de views
    con 'first=2' indicamos que queremos los 2 primeros (tambien aparece en la documentacion)
    https://dev.twitch.tv/docs/api/reference/#get-clips
    */
        $url = "https://api.twitch.tv/helix/streams?game_id=" . trim($id). '&first=2';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $this->accessToken,
            "Client-Id: " . $this->clientID
        ]);

        $resp = curl_exec($ch);
        $data = json_decode($resp, true);
        curl_close($ch);

        return isset($data['data']) ? ($data['data']) : [];
    }

}