<?php

namespace App\Controller;

use App\Service\GameService;
use PDOException;
use Exception;
use App\Core\ApiResponseTrait;

class GameController {
    use ApiResponseTrait;
    private GameService $gameService;

    public function __construct(GameService $GameService)
    {
        $this->gameService = $GameService;
    }

    public function getAllGames(): void
    {
        $games = $this->gameService->getAllGames();
        $this->jsonResponse(array_map(fn($u) => $u->toArray(), $games));
    }

    public function getGamesById(int $id): void
    {
        $game = $this->gameService->getGameById($id);

        if ($game) {
            $this->jsonResponse($game->toArray());
        } else {
            $this->jsonResponse(['error' => 'Game not found'], 404);
        }
    }

    public function getGamesByName(string $name): void
    {
        $games = $this->gameService->getGamesByName($name);

        if ($games) {
            $this->jsonResponse(array_map(fn($g) => $g->toArray(), $games));
        } else {
            $this->jsonResponse(['error' => 'No games found with that name'], 404);
        }
    }
}
