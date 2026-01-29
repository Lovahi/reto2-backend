<?php

namespace App\Model;


class UserEvent {
    private ?int $userId;
    private ?int $eventId;

    public function __construct(?int $userId, ?int $eventId) {
        $this->userId = $userId;
        $this->eventId = $eventId;
    }

    // Getters and Setters
    public function getUserId(): ?int { return $this->userId; }
    public function setUserId(?int $userId): void { $this->userId = $userId; }

    public function getEventId(): ?int { return $this->eventId; }
    public function setEventId(?int $eventId): void { $this->eventId = $eventId; }

    public function toArray(): array {
        return [
            'user_id' => $this->userId,
            'event_id' => $this->eventId,
        ];
    }
}
