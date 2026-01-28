<?php

namespace App\Service;

use App\Repository\GameRepository;
use App\Model\Game;
use App\DTO\GameDTO;
use App\Core\ImageHelper;

class GameService
{
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
            ImageHelper::getImageUrl($game->getImage(), 'games'),
            $game->getDescription()
        );
    }

    public function getGamesPaginated(int $page): array {
        $games = $this->GameRepository->getGamesPaginated($page);
        return array_map(fn($game) => new GameDTO(
            $game->getId(),
            $game->getTitle(),
            $game->getGenre(),
            $game->getPlatforms(),
            ImageHelper::getImageUrl($game->getImage(), 'games'),
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
            ImageHelper::getImageUrl($game->getImage(), 'games'),
            $game->getDescription()
        ), $games);
    }
}
