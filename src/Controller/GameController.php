<?php

namespace App\Controller;

use App\Service\GameService;
use PDOException;
use Exception;

class GameController
{
    private GameService $gameService;

    public function __construct(GameService $GameService)
    {
        $this->gameService = $GameService;
    }

    private function jsonResponse(mixed $data, int $status = 200): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($status);
        echo json_encode($data);
    }

    private function getJsonInput(): ?array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function handleDatabaseException(PDOException $e): void
    {
        switch ($e->getCode()) {
            case 23000:
                $this->jsonResponse(['error' => 'The Gamename or email is already in use'], 400);
                break;
            default:
                $this->jsonResponse(['error' => 'A database error occurred'], 500);
                break;
        }
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
