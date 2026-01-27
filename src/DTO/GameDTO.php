<?php

namespace App\DTO;

class GameDTO {
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
        array $platforms, 
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