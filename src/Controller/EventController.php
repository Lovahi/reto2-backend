<?php

namespace App\Controller;

use App\Service\EventService;
use PDOException;
use Exception;
use App\Core\ApiResponseTrait;

class EventController {
    use ApiResponseTrait;

    private EventService $eventService;

    public function __construct(EventService $eventService) {
        $this->eventService = $eventService;
    }

    public function getEventById(int $id): void {
        $event = $this->eventService->getEventById($id);

        if ($event) {
            $this->jsonResponse($event->toArray());
        } else {
            $this->jsonResponse(['error' => 'Event not found'], 404);
        }
    }

    public function getEvents(): void {
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $events = $this->eventService->getEventsPaginated($page);

        if (!empty($events)) {
            $this->jsonResponse(array_map(fn($e) => $e->toArray(), $events));
        } else {
            $this->jsonResponse(['error' => 'No events found for this page'], 404);
        }
    }

    public function getEventsByName(string $title): void {
        $events = $this->eventService->getEventsByName($title);

        if ($events) {
            $this->jsonResponse(array_map(fn($e) => $e->toArray(), $events));
        } else {
            $this->jsonResponse(['error' => 'No events found with that name'], 404);
        }
    }

    public function createEvent(): void {
        $data = $this->getJsonInput();

        if (!$data || !\is_array($data)) {
            $this->jsonResponse(['error' => 'Invalid JSON or empty body'], 400);
            return;
        }

        try {
            if ($this->eventService->createEvent($data)) {
                $this->jsonResponse(['message' => 'Event created successfully'], 201);
            } else {
                $this->jsonResponse(['error' => 'Failed to create event'], 400);
            }
        } catch (PDOException $e) {
            $this->handleDatabaseException($e);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
