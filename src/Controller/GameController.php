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
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $games = $this->gameService->getGamesPaginated($page);

        if (!empty($games)) {
            $this->jsonResponse(array_map(fn($g) => $g->toArray(), $games));
        } else {
            $this->jsonResponse(['error' => 'No games found for this page'], 404);
        }
    }

    public function getGamesById(int $id): void {
        $game = $this->gameService->getGameById($id);

        if ($game) {
            $this->jsonResponse($game->toArray());
        } else {
            $this->jsonResponse(['error' => 'Game not found'], 404);
        }
    }

    public function getGamesByName(string $name): void {
        $games = $this->gameService->getGamesByName($name);

        if ($games) {
            $this->jsonResponse(array_map(fn($g) => $g->toArray(), $games));
        } else {
            $this->jsonResponse(['error' => 'No games found with that name'], 404);
        }
    }
}
