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
        $filters = $_GET;
        $page = isset($filters['page']) ? (int) $filters['page'] : 1;
        unset($filters['page']);
        
        $events = $this->eventService->getEventsByFilter($filters, $page);
        $this->jsonResponse(array_map(fn($e) => $e->toArray(), $events));
    }

    public function getEventsPagesCounter(): void {
        $filters = $_GET;
        unset($filters['page']);
        
        $counter = $this->eventService->getEventsPagesCounter($filters);
        $this->jsonResponse(['total' => $counter]);
    }

    public function createEvent(): void {
        $data = $this->getRequestInput();
        
        if (empty($data)) {
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
