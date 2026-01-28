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

    public function getEventsPaginated(int $page, int $limit = 9): array {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("SELECT * FROM events LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

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

    public function getEventsByTitle(string $title): array {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE titulo LIKE :title");
        $stmt->execute(['title' => '%' . $title . '%']);
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

    //////////////NUEVO////////////////////
    public function isUserRegistered(int $userId, int $eventId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM event_registrations WHERE user_id = :user_id AND event_id = :event_id");
        $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
        return $stmt->fetchColumn() > 0;
    }

    public function registerUser(int $userId, int $eventId): bool {
        // Usamos transacciones para asegurar que se resta la plaza Y se inscribe al usuario
        try {
            $this->db->beginTransaction();

            // 1. Insertar inscripción
            $stmt = $this->db->prepare("INSERT INTO event_registrations (user_id, event_id) VALUES (:user_id, :event_id)");
            $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);

            // 2. Restar una plaza
            $stmtUpdate = $this->db->prepare("UPDATE events SET plazasLibres = plazasLibres - 1 WHERE id = :id AND plazasLibres > 0");
            $stmtUpdate->execute(['id' => $eventId]);
            
            // Verificar si se actualizó la fila (si no, es que no había plazas)
            if ($stmtUpdate->rowCount() === 0) {
                 throw new \Exception("No quedan plazas disponibles");
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function unregisterUser(int $userId, int $eventId): bool {
        try {
            $this->db->beginTransaction();

            // 1. Borrar inscripción
            $stmt = $this->db->prepare("DELETE FROM event_registrations WHERE user_id = :user_id AND event_id = :event_id");
            $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);

            if ($stmt->rowCount() === 0) {
                 throw new \Exception("Usuario no estaba inscrito");
            }

            // 2. Sumar una plaza
            $stmtUpdate = $this->db->prepare("UPDATE events SET plazasLibres = plazasLibres + 1 WHERE id = :id");
            $stmtUpdate->execute(['id' => $eventId]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>