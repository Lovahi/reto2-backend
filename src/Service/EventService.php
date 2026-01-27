<?php

namespace App\Service;

use App\Repository\EventRepository;
use App\Model\Event;
use App\DTO\EventDTO;
use App\Enum\Type;

class EventService {
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository) {
        $this->eventRepository = $eventRepository;
    }

    public function getEventById(int $id): ?EventDTO {
        $event = $this->eventRepository->getEventById($id);
        if (!$event) return null;

        return new EventDTO($event->getId(), $event->getTitle(), $event->getType(), $event->getDate(), $event->getHour(), $event->getAvailablePlaces(), $event->getImage(), $event->getDescription());
    }

    public function getEventsPaginated(int $page): array {
        $events = $this->eventRepository->getEventsPaginated($page);
        return array_map(fn($event) => new EventDTO(
            $event->getId(),
            $event->getTitle(),
            $event->getType(),
            $event->getDate(),
            $event->getHour(),
            $event->getAvailablePlaces(),
            $event->getImage(),
            $event->getDescription()
        ), $events);
    }

    
    public function getEventsByName(string $title): array {
        $events = $this->eventRepository->getEventsByTitle($title);
        return array_map(fn($event) => new EventDTO(
            $event->getId(),
            $event->getTitle(),
            $event->getType(),
            $event->getDate(),
            $event->getHour(),
            $event->getAvailablePlaces(),
            $event->getImage(),
            $event->getDescription()
        ), $events);
    }

    public function createEvent(array $data): bool {
        if (empty($data['title']) || empty($data['type']) || empty($data['date']) || empty($data['hour']) || empty($data['availablePlaces']) || empty($data['image']) || empty($data['description'])) {
            throw new \Exception("Missing required fields: title, type, date, hour, availablePlaces, image, description");
        }

        $event = new Event(
            null,
            $data['title'],
            Type::from($data['type']),
            $data['date'],
            $data['hour'],
            $data['availablePlaces'],
            $data['image'],
            $data['description']
        );

        return $this->eventRepository->createEvent($event);
    }
}
