<?php

namespace App\Service;

use App\Repository\EventRepository;
use App\Model\Event;
use App\DTO\EventDTO;
use App\Enum\Type;

class EventService
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getEventById(int $id): ?EventDTO
    {
        $event = $this->eventRepository->getEventById($id);
        if (!$event)
            return null;

        return new EventDTO($event->getId(), $event->getTitle(), $event->getType(), $event->getDate(), $event->getHour(), $event->getAvailablePlaces(), $event->getImage(), $event->getDescription());
    }

    public function getEventsPaginated(int $page): array
    {
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


    public function getEventsByName(string $title): array
    {
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

    public function createEvent(array $data): bool
    {
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

    public function signupUser(int $userId, int $eventId): array
    {
        // 1. Verificar si el evento existe
        $event = $this->getEventById($eventId);
        if (!$event) {
            return ['success' => false, 'message' => 'Evento no encontrado', 'code' => 404];
        }

        // 2. Verificar plazas (aunque el repo también lo hace, es bueno comprobar antes)
        if ($event->getAvailablePlaces() <= 0) {
            return ['success' => false, 'message' => 'No hay plazas disponibles', 'code' => 409];
        }

        // 3. Verificar si ya está inscrito
        if ($this->eventRepository->isUserRegistered($userId, $eventId)) {
            return ['success' => false, 'message' => 'El usuario ya está inscrito', 'code' => 409];
        }

        // 4. Intentar registro
        if ($this->eventRepository->registerUser($userId, $eventId)) {
            return ['success' => true, 'message' => 'Inscripción realizada con éxito', 'code' => 201];
        }

        return ['success' => false, 'message' => 'Error al realizar la inscripción', 'code' => 500];
    }

    public function cancelSignupUser(int $userId, int $eventId): array
    {
        // 1. Verificar si está inscrito
        if (!$this->eventRepository->isUserRegistered($userId, $eventId)) {
            return ['success' => false, 'message' => 'El usuario no está inscrito en este evento', 'code' => 404];
        }

        // 2. Intentar borrar registro
        if ($this->eventRepository->unregisterUser($userId, $eventId)) {
            return ['success' => true, 'message' => 'Inscripción cancelada', 'code' => 200];
        }

        return ['success' => false, 'message' => 'Error al cancelar la inscripción', 'code' => 500];
    }
}
?>