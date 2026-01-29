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


    /**
     * Inscribe un usuario en un evento
     * @param int $userId
     * @param int $eventId
     * @return array{code: int, message: string, success: bool}
     */
    public function inscribirUsuario(int $userId, int $eventId): array
    {
        $eventoModelo = $this->eventRepository->getEventById($eventId);

        if (!$eventoModelo) {
            return ['success' => false, 'message' => 'El evento no existe', 'code' => 404];
        }

        if ($this->eventRepository->verificarUsuarioRegistrado($userId, $eventId)) {
            return ['success' => false, 'message' => 'Ya estás inscrito', 'code' => 409];
        }
        // Validar plazas
        if ((int) $eventoModelo->getAvailablePlaces() <= 0) {
            return ['success' => false, 'message' => 'No quedan plazas libres', 'code' => 409];
        }
        //Inscribir
        if ($this->eventRepository->inscribirUsuario($userId, $eventId)) {
            return ['success' => true, 'message' => 'Inscrito correctamente', 'code' => 201];
        }

        return ['success' => false, 'message' => 'Error interno', 'code' => 500];
    }

    /**
     * Cancela la inscripción de un usuario en un evento
     * @param int $userId
     * @param int $eventId
     * @return array{code: int, message: string, success: bool}
     */ 
    public function cancelarInscripcionUsuario(int $userId, int $eventId): array
    {
        if (!$this->eventRepository->verificarUsuarioRegistrado($userId, $eventId)) {
            return ['success' => false, 'message' => 'No estás inscrito', 'code' => 404];
        }

        if ($this->eventRepository->cancelarInscripcion($userId, $eventId)) {
            return ['success' => true, 'message' => 'Cancelado correctamente', 'code' => 200];
        }

        return ['success' => false, 'message' => 'Error al cancelar', 'code' => 500];
    }
}
?>