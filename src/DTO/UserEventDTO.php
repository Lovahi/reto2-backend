<?php

namespace App\DTO;


class UserEventDTO {
    private ?int $user_id;
    private ?int $event_id;

    public function __construct(?int $user_id, ?int $event_id) {
        $this->user_id = $user_id;
        $this->event_id = $event_id;
    }

    public function toArray(): array {
        return [
            'user_id' => $this->user_id,
            'event_id' => $this->event_id,
        ];
    }
}
