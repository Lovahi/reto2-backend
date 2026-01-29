<?php

namespace App\Service;

use App\Repository\GameRepository;
use App\DTO\GameDTO;

class GameService {
    private GameRepository $GameRepository;

    public function __construct(GameRepository $GameRepository) {
        $this->GameRepository = $GameRepository;
    }

    public function getGameById(int $id): ?GameDTO {
        $game = $this->GameRepository->getGameById($id);
        if (!$game) return null;

        return new GameDTO(
            $game->getId(),
            $game->getTitle(),
            $game->getGenre(),
            $game->getPlatforms(),
            $game->getImage(),
            $game->getDescription()
        );
    }

    public function getGamesByFilter(array $filters, int $page = 1): array {
        $games = $this->GameRepository->getGamesByFilter($filters, $page);
        return array_map(fn($game) => new GameDTO(
            $game->getId(),
            $game->getTitle(),
            $game->getGenre(),
            $game->getPlatforms(),
            $game->getImage(),
            $game->getDescription()
        ), $games);
    }

    public function getGamesPagesCounter(): int
    {
        return $this->GameRepository->getGamesPagesCounter();
    }
}
