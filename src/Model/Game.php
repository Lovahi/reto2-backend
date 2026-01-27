<?php

namespace App\Model;

class Game {
    private ?int $id;
    private string $title;
    private string $genre;
    private array $platforms;
    private string $image;
    private string $description;

    public function __construct(
        ?int $id, 
        string $title, 
        string $genre, 
        Array $platforms, 
        string $image, 
        string $description
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->genre = $genre;
        $this->platforms = $platforms;
        $this->image = $image;
        $this->description = $description;
    }

    // Getters and Setters
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getGenre(): string { return $this->genre; }
    public function setGenre(string $genre): void { $this->genre = $genre; }

    public function getPlatforms(): Array { return $this->platforms; }
    public function setPlatforms(array $platforms): void { $this->platforms = $platforms; }

    public function getImage(): string { return $this->image; }
    public function setImage(string $image): void { $this->image = $image; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): void { $this->description = $description; }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'genre' => $this->genre,
            'platforms' => $this->platforms,
            'image' => $this->image,
            'description' => $this->description
        ];
    }
}