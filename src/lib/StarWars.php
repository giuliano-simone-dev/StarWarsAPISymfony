<?php
namespace App\lib;

use GuzzleHttp\Client;
class StarWars{
    private $client;
    public function __construct() {
        $this->client = new Client([
            "base_uri" => "https://swapi.dev/api/",
            "timeout" => 10.0
        ]);
    }

    public function getAllCharacters(){
        $characters = [];
        $next = 'people/?format=json';
        /*
        El servidor no provee un endpoint que devuelva todos los personajes de una, asi que iteramos sobre la respuesta hasta
        que no haya página siguiente.
        */
        while($next != null){
            $res = $this->client->get($next);
            if($res->getStatusCode()!=200){
                //No uso el Codigo del Servidor ya que no es el estado real de este servicio.
                throw new \Exception("External API not working at the moment",503);
            }
            $response = json_decode($res->getBody()->getContents());
            $next = $response->next;
            //Unimos el array ya existente con el que acabamos de obtener
            $characters = array_merge($characters,$response->results);
        }

        
        return $characters;
    }

    public function getPageCharacters(int $page){
        try{
            $res = $this->client->get("people/?page=$page&format=json");
            $response = json_decode($res->getBody()->getContents());
            //Devolvemos la respuesta y el next para formatearlo a la url del servidor
            return ["next"=>$response->next,"results"=>$response->results];
        }catch(\Throwable $error){
            //Usamos un switch por si más tarde necesitamos tener en cuenta más Codigos HTTP
            switch($error->getCode()){
                    case 404: throw new \Exception("Page $page not found", 404);
                    default: throw new \Exception("External API not working at the moment",503);
                }
        }
    }
}