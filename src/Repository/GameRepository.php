<?php

namespace App\Repository;

use App\Model\Game;
use PDO;

class GameRepository {
    private PDO $db;

    public function __construct(PDO $db) {
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

    public function getGamesByFilter(array $filters, int $page = 1, int $limit = 9): array {
        $sql = "SELECT * FROM games WHERE 1=1";
        $params = [];

        if (!empty($filters['titulo'])) {
            $sql .= " AND titulo LIKE :titulo";
            $params['titulo'] = '%' . $filters['titulo'] . '%';
        }

        if (!empty($filters['genero'])) {
            $sql .= " AND genero = :genero";
            $params['genero'] = $filters['genero'];
        }

        if (!empty($filters['plataforma'])) {
            $sql .= " AND plataformas LIKE :plataforma";
            $params['plataforma'] = '%' . $filters['plataforma'] . '%';
        }

        $page = max(1, $page);
        $offset = ($page - 1) * $limit;
        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();

        $games = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $games[] = new Game(
                (int)$row['id'],
                $row['titulo'],
                $row['genero'],
                json_decode($row['plataformas'], true) ?: [],
                $row['imagen'],
                $row['descripcion']
            );
        }
        return $games;
    }

    public function getGamesPagesCounter(array $filters = []): int {
        $sql = "SELECT COUNT(*) FROM games WHERE 1=1";
        $params = [];

        if (!empty($filters['titulo'])) {
            $sql .= " AND titulo LIKE :titulo";
            $params['titulo'] = '%' . $filters['titulo'] . '%';
        }

        if (!empty($filters['genero'])) {
            $sql .= " AND genero = :genero";
            $params['genero'] = $filters['genero'];
        }

        if (!empty($filters['plataforma'])) {
            $sql .= " AND plataformas LIKE :plataforma";
            $params['plataforma'] = '%' . $filters['plataforma'] . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }
}
