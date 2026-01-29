<?php

namespace App\Repository;

use App\Model\UserEvent;
use PDO;

class UserEventRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getUserEvents(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM user_events WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        $userEvents = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $userEvents[] = new UserEvent(
                (int)$row['user_id'], 
                (int)$row['event_id']
            );
        }
        return $userEvents;
    }

    public function getEventsUsers(int $eventId): array {
        $stmt = $this->db->prepare("SELECT * FROM user_events WHERE event_id = :event_id");
        $stmt->execute(['event_id' => $eventId]);
        $eventsUsers = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventsUsers[] = new UserEvent(
                (int)$row['user_id'], 
                (int)$row['event_id']
            );
        }
        return $eventsUsers;
    }

    public function signUpEvent(int $userId, int $eventId): bool {
        $stmt = $this->db->prepare('INSERT INTO user_events (user_id, event_id) VALUES (:user_id, :event_id)');
        $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
        return $stmt->rowCount() > 0;
    }

    public function signDownEvent(int $userId, int $eventId): bool {
        $stmt = $this->db->prepare('DELETE FROM user_events WHERE user_id = :user_id AND event_id = :event_id');
        $stmt->execute(['user_id' => $userId, 'event_id' => $eventId]);
        return $stmt->rowCount() > 0;
    }
}
