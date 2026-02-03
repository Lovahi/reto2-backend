<?php

namespace App\Model;

use App\Enum\Type;

class Event {
    private ?int $id;
    private string $title;
    private Type $type;
    private string $date;
    private string $hour;
    private int $availablePlaces;
    private string $image;
    private string $description;
    private int $createdBy;

    public function __construct(?int $id, string $title, Type $type, string $date, string $hour, int $availablePlaces, string $image, string $description, int $createdBy) {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->date = $date;
        $this->hour = $hour;
        $this->availablePlaces = $availablePlaces;
        $this->image = $image;
        $this->description = $description;
        $this->createdBy = $createdBy;
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getType(): Type { return $this->type; }
    public function setType(Type $type): void { $this->type = $type; }

    public function getDate(): string { return $this->date; }
    public function setDate(string $date): void { $this->date = $date; }

    public function getHour(): string { return $this->hour; }
    public function setHour(string $hour): void { $this->hour = $hour; }

    public function getAvailablePlaces(): int { return $this->availablePlaces; }
    public function setAvailablePlaces(int $availablePlaces): void { $this->availablePlaces = $availablePlaces; }

    public function getImage(): string { return $this->image; }
    public function setImage(string $image): void { $this->image = $image; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }
    
    public function getCreatedBy(): int { return $this->createdBy; }
    public function setCreatedBy(int $createdBy): void { $this->createdBy = $createdBy; }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type->value,
            'date' => $this->date,
            'hour' => $this->hour,
            'availablePlaces' => $this->availablePlaces,
            'image' => $this->image,
            'description' => $this->description,
            'createdBy' => $this->createdBy
        ];
    }
}
