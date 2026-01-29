<?php

namespace App\Controller;

use App\Service\GameService;
use PDOException;
use Exception;
use App\Core\ApiResponseTrait;

class GameController {
    use ApiResponseTrait;

    private GameService $gameService;

    public function __construct(GameService $GameService) {
        $this->gameService = $GameService;
    }

    public function getGames(): void {
        $filters = $_GET;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;
        unset($filters['page']);
        
        $games = $this->gameService->getGamesByFilter($filters, $page);
        $this->jsonResponse(array_map(fn($u) => $u->toArray(), $games));
    }

    public function getGamesById(int $id): void {
        $game = $this->gameService->getGameById($id);

        if ($game) {
            $this->jsonResponse($game->toArray());
        } else {
            $this->jsonResponse(['error' => 'Game not found'], 404);
        }
    }
    
    public function getGamesPagesCounter(): void {
        $filters = $_GET;
        unset($filters['page']);
        
        $counter = $this->gameService->getGamesPagesCounter($filters);
        $this->jsonResponse(['total' => $counter]);
    }

}
