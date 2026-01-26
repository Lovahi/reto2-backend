<?php

namespace App\Repository;

use App\Model\Event;
use App\Enum\Type;
use PDO;

class EventRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getEventById(int $id): ?Event {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) return null;

        return new Event(
            (int)$row['id'], 
            $row['titulo'], 
            Type::from($row['tipo']), 
            $row['fecha'], 
            $row['hora'], 
            (int)$row['plazasLibres'], 
            $row['imagen'], 
            $row['descripcion']
        );
    }

    public function getAllEvents(): array {
        $stmt = $this->db->query("SELECT * FROM events");
        $events = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = new Event(
                (int)$row['id'], 
                $row['titulo'], 
                Type::from($row['tipo']), 
                $row['fecha'], 
                $row['hora'], 
                (int)$row['plazasLibres'], 
                $row['imagen'], 
                $row['descripcion']
            );
        }
        return $events;
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
            $event->setId((int)$this->db->lastInsertId());
        }
        return $result;
    }
}
?>