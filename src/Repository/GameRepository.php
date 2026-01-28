<?php

namespace App\Repository;

use App\Model\Game;
use PDO;

class GameRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
    
    public function getGameById(int $id): ?Game{
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$row) return null;
        $platforms = json_decode($row['plataformas'], true) ?: [];
        
        return new Game(
            (int)$row['id'], 
            $row['titulo'], 
            $row['genero'], 
            $platforms, 
            $row['imagen'], 
            $row['descripcion']
        );
    }
    
    public function getGamesPaginated(int $page, int $limit = 9): array {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("SELECT * FROM games LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $platforms = json_decode($row['plataformas'], true) ?: [];
            $games[] = new Game(
                (int)$row['id'], 
                $row['titulo'], 
                $row['genero'], 
                $platforms, 
                $row['imagen'], 
                $row['descripcion']
            );
        }
        return $games;
    }
    
    public function getGamesByName(string $name): array{
        $stmt = $this->db->prepare("SELECT * FROM games WHERE titulo LIKE :name");
        $stmt->execute(['name' => '%' . $name . '%']);
        
        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $platforms = json_decode($row['plataformas'], true) ?: [];
            $games[] = new Game(
                (int)$row['id'], 
                $row['titulo'], 
                $row['genero'], 
                $platforms, 
                $row['imagen'], 
                $row['descripcion']
            );
        }
        return $games;
    }
}