<?php

namespace App\Repository;

use App\Model\Event;
use App\Enum\Type;
use PDO;

class EventRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getEventById(int $id): ?Event
    {
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

    public function getAllEvents(): array
    {
        $stmt = $this->db->query("SELECT * FROM events");
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

    public function getEventsPaginated(int $page, int $limit = 9): array
    {
        $offset = ($page - 1) * $limit;
        $stmt = $this->db->prepare("SELECT * FROM events LIMIT :limit OFFSET :offset");
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

    public function getEventsByTitle(string $title): array
    {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE titulo LIKE :title");
        $stmt->execute(['title' => '%' . $title . '%']);
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

    public function createEvent(Event $event): bool
    {
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

    //////////////NUEVO////////////////////
    /**
     * Verifica si un usuario está registrado en un evento
     * @param int $userId
     * @param int $eventId
     * @return bool
     */
    public function verificarUsuarioRegistrado(int $userId, int $eventId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_events WHERE user_id = :user_id AND event_id = :event_id");
        $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Inscribe a un usuario en un evento
     * @param int $userId
     * @param int $eventId
     * @throws \Exception
     * @return bool
     */
    public function inscribirUsuario(int $userId, int $eventId): bool
    {
        $resultado = false;
        try {
            //Esta transaccion es por si acaso el servidor o algo falla en medio del proceso
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("INSERT INTO user_events (user_id, event_id) VALUES (:user_id, :event_id)");
            $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);

            $stmtUpdate = $this->db->prepare("UPDATE events SET plazasLibres = plazasLibres - 1 WHERE id = :id AND plazasLibres > 0");
            $stmtUpdate->execute(['id' => $eventId]);
            //Si no quedan plazas, salta error
            if ($stmtUpdate->rowCount() === 0) {
                throw new \Exception("No quedan plazas disponibles");
            }
            $this->db->commit();
            $resultado = true;

        } catch (\Exception $e) {
            $this->db->rollBack();
        }
        return $resultado;
    }
    /**
     * Cancela la inscripción de un usuario en un evento
     * @param int $userId
     * @param int $eventId
     * @throws \Exception
     * @return bool
     */
    public function cancelarInscripcion(int $userId, int $eventId): bool
    {
        $resultado = false;
        try {
            $this->db->beginTransaction();
            // Borrar inscripción
            $stmt = $this->db->prepare("DELETE FROM user_events WHERE user_id = :user_id AND event_id = :event_id");
            $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);

            if ($stmt->rowCount() === 0) {
                throw new \Exception("Usuario no estaba inscrito");
            }

            // Sumar una plaza
            $stmtUpdate = $this->db->prepare("UPDATE events SET plazasLibres = plazasLibres + 1 WHERE id = :id");
            $stmtUpdate->execute(['id' => $eventId]);

            $this->db->commit();
            $resultado = true;
        } catch (\Exception $e) {
            $this->db->rollBack();
        }
        return $resultado;
    }
}
?>