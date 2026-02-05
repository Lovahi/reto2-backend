<?php

namespace App\Service;

use App\Repository\EventRepository;
use App\Model\Event;
use App\DTO\EventDTO;
use App\Enum\Type;
use App\Core\ImageHelper;

class EventService {
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository) {
        $this->eventRepository = $eventRepository;
    }

    public function getEventById(int $id): ?EventDTO {
        $event = $this->eventRepository->getEventById($id);
        if (!$event)
            return null;

        return new EventDTO(
            $event->getId(),
            $event->getTitle(),
            $event->getType(),
            $event->getDate(),
            $event->getHour(),
            $event->getAvailablePlaces(),
            ImageHelper::getImageUrl($event->getImage(), 'events'),
            $event->getDescription()
        );
    }

    public function getEventsByFilter(array $filters, int $page = 1): array {
        $events = $this->eventRepository->getEventsByFilter($filters, $page);
        return array_map(fn($event) => new EventDTO(
            $event->getId(),
            $event->getTitle(),
            $event->getType(),
            $event->getDate(),
            $event->getHour(),
            $event->getAvailablePlaces(),
            ImageHelper::getImageUrl($event->getImage(), 'events'),
            $event->getDescription()
        ), $events);
    }

    public function getEventsPagesCounter(array $filters = []): int {
        return $this->eventRepository->getEventsPagesCounter($filters);
    }

    public function createEvent(array $data, int $createdBy): bool {
        if (empty($data['title']) || empty($data['type']) || empty($data['date']) || empty($data['hour']) || empty($data['availablePlaces']) || empty($data['image']) || empty($data['description'])) {
            throw new \Exception("Missing required fields: title, type, date, hour, availablePlaces, image, description");
        }

        $imageData = $data['image'] ?? null;
        $imageName = is_array($imageData) ? ImageHelper::saveImage($imageData, 'events') : (is_string($imageData) ? $imageData : '');

        $event = new Event(
            null,
            $data['title'],
            Type::from($data['type']),
            $data['date'],
            $data['hour'],
            $data['availablePlaces'],
            $imageName,
            $data['description'],
            $createdBy
        );

        return $this->eventRepository->createEvent($event);
    }
    public function incrementAvailablePlaces(int $eventId): bool {
        return $this->eventRepository->incrementAvailablePlaces($eventId);
    }

    public function decrementAvailablePlaces(int $eventId): bool {
        return $this->eventRepository->decrementAvailablePlaces($eventId);
    }
}
