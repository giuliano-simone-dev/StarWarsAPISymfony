<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use App\lib\StarWars;


class CharactersController extends AbstractController
{
    private StarWars $starWarsHandler;
    private CacheInterface $cache;

    private int $CacheTTL = 300; //300 segundos (5 minutos) 
    
    public function __construct(StarWars $starWarsHandler, CacheInterface $cache){
        $this->starWarsHandler = $starWarsHandler;
        $this->cache = $cache;
    }
    //Normalmente usaria GET para ambas peticiones, pero en el enunciado requeria que sean del tipo POST
    #[Route('api/v1/characters/all', methods:["POST"] ,name: 'getAllCharacters')]
    public function getAllCharacters(): JsonResponse
    {
        try{
            $people = $this->cache->get('star_wars_characters_all', function($item){
                $item->expiresAfter($this->CacheTTL);
                return $this->starWarsHandler->getAllCharacters();
            });
        }catch(\Throwable $error){
            return new JsonResponse(["error"=>$error->getMessage()], $error->getCode()?:500);
        }
        return $this->json([
            "results" => $people
        ]);
    }

    #[Route('api/v1/characters/page/{page}', defaults:["page"=>1], methods:["POST"] ,name: 'getCharactersByPage')]
    public function getCharactersByPage($page): JsonResponse
    {
            try{
                $people = $this->cache->get('star_wars_characters_page_'.$page, function($item) use ($page) {
                    $item->expiresAfter($this->CacheTTL);
                    return $this->starWarsHandler->getPageCharacters($page);
                });
            }catch(\Throwable $error){
                return new JsonResponse(["error"=>$error->getMessage()], $error->getCode()?:500);
            }
            //Nos fijamos si hay una siguiente pagina y si esta la variable de entorno para el string de URL
            if($people["next"] && isset($_ENV["APP_URL_STRING"])){
                $people["next"] = $_ENV["APP_URL_STRING"]."/api/v1/characters/".($page+1);
            }else{
                unset($people["next"]);
            }
        
        return new JsonResponse($people);
    }
}
