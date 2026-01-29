<?php

namespace App\DTO;

use App\Enum\Type;

class EventDTO {
    private ?int $id;
    private string $title;
    private Type $type;
    private string $date;
    private string $hour;
    private string $availablePlaces;
    private string $image;
    private string $description;

    public function __construct(?int $id, string $title, Type $type, string $date, string $hour, string $availablePlaces, string $image, string $description) {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->date = $date;
        $this->hour = $hour;
        $this->availablePlaces = $availablePlaces;
        $this->image = $image;
        $this->description = $description;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'date' => $this->date,
            'hour' => $this->hour,
            'availablePlaces' => $this->availablePlaces,
            'image' => $this->image,
            'description' => $this->description
        ];
    }
}
