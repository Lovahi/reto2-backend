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

    public function getAllGames(): array {
        $games = $this->GameRepository->getAllGames();
        return array_map(fn($game) => new GameDTO(
            $game->getId(),
            $game->getTitle(),
            $game->getGenre(),
            $game->getPlatforms(),
            $game->getImage(),
            $game->getDescription()
        ), $games);
    }

    public function getGamesByName(string $name): array {
        $games = $this->GameRepository->getGamesByName($name);
        return array_map(fn($game) => new GameDTO(
            $game->getId(),
            $game->getTitle(),
            $game->getGenre(),
            $game->getPlatforms(),
            $game->getImage(),
            $game->getDescription()
        ), $games);
    }

}
