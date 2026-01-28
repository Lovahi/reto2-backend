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



    public function inscribirUsuario(int $userId, int $eventId): array
    {
        $eventoDTO = $this->getEventById($eventId);
        if (!$eventoDTO) {
            return [
                'success' => false,
                'message' => 'El evento no existe',
                'code' => 404
            ];
        }

        $estaInscrito = $this->eventRepository->verificarUsuarioRegistrado($userId, $eventId);
        if ($estaInscrito) {
            return [
                'success' => false,
                'message' => 'El usuario ya está inscrito en este evento',
                'code' => 409
            ];
        }

        // if ((int) $eventoDTO->availablePlaces <= 0) {
        //     return [
        //         'success' => false,
        //         'message' => 'No quedan plazas disponibles',
        //         'code' => 409
        //     ];
        // }
        $inscripcionExitosa = $this->eventRepository->inscribirUsuario($userId, $eventId);

        if ($inscripcionExitosa) {
            return [
                'success' => true,
                'message' => 'Inscripción realizada con éxito',
                'code' => 201
            ];
        } else {
            // Si el repo devuelve false, es que falló la transacción (ej: error SQL raro)
            return [
                'success' => false,
                'message' => 'Error interno al procesar la inscripción',
                'code' => 500
            ];
        }
    }

    public function cancelarInscripcionUsuario(int $userId, int $eventId): array
    {
        // 1. PASO: ¿El usuario está inscrito?
        // No podemos borrar lo que no existe.
        $estaInscrito = $this->eventRepository->verificarUsuarioRegistrado($userId, $eventId);

        if (!$estaInscrito) {
            return [
                'success' => false,
                'message' => 'El usuario no está inscrito en este evento',
                'code' => 404 // No encontrado
            ];
        }

        // 2. PASO: Intentar borrar (Borrar + Sumar plaza)
        $cancelacionExitosa = $this->eventRepository->cancelarInscripcion($userId, $eventId);

        if ($cancelacionExitosa) {
            return [
                'success' => true,
                'message' => 'Inscripción cancelada correctamente',
                'code' => 200
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error al cancelar la inscripción',
                'code' => 500
            ];
        }
    }

}
?>