<?php

namespace App\Repository;

use App\Model\Event;
use App\Enum\Type;
use PDO;

class EventRepository
{
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getEventById(int $id): ?Event {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row)
            return null;

        return new Event(
            (int) $row['id'],
            $row['titulo'],
            Type::from($row['tipo']),
            $row['fecha'],
            $row['hora'],
            (int) $row['plazasLibres'],
            $row['imagen'],
            $row['descripcion']
        );
    }

    public function getEventsByFilter(array $filters, int $page = 1, int $limit = 9): array {
        $sql = "SELECT * FROM events WHERE 1=1";
        $params = [];

        if (!empty($filters['titulo'])) {
            $sql .= " AND titulo LIKE :titulo";
            $params['titulo'] = '%' . $filters['titulo'] . '%';
        }

        if (!empty($filters['tipo'])) {
            $sql .= " AND tipo = :tipo";
            $params['tipo'] = $filters['tipo'];
        }

        if (!empty($filters['fecha'])) {
            $sql .= " AND fecha = :fecha";
            $params['fecha'] = $filters['fecha'];
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

        $events = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = new Event(
                (int) $row['id'],
                $row['titulo'],
                Type::from($row['tipo']),
                $row['fecha'],
                $row['hora'],
                (int) $row['plazasLibres'],
                $row['imagen'],
                $row['descripcion']
            );
        }
        return $events;
    }

    public function getEventsPagesCounter(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM events");
        return (int) $stmt->fetchColumn();
    }

    public function createEvent(Event $event): bool {
        $stmt = $this->db->prepare("INSERT INTO events (titulo, tipo, fecha, hora, plazasLibres, imagen, descripcion) VALUES (:titulo, :tipo, :fecha, :hora, :plazasLibres, :imagen, :descripcion)");
        $result = $stmt->execute([
            'titulo' => $event->getTitle(),
            'tipo' => $event->getType()->value,
            'fecha' => $event->getDate(),
            'hora' => $event->getHour(),
            'plazasLibres' => $event->getAvailablePlaces(),
            'imagen' => $event->getImage(),
            'descripcion' => $event->getDescription(),
        ]);
        if ($result) {
            $event->setId((int) $this->db->lastInsertId());
        }
        return $result;
    }
}
