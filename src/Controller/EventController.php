<?php

namespace App\Controller;

use App\Service\EventService;
use PDOException;
use Exception;

class EventController {
    private EventService $eventService;

    public function __construct(EventService $eventService) {
        $this->eventService = $eventService;
    }

    private function jsonResponse(mixed $data, int $status = 200): void {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($status);
        echo json_encode($data);
    }

    private function getJsonInput(): ?array {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }

    private function handleDatabaseException(PDOException $e): void {
        switch ($e->getCode()) {
            case 23000:
                $this->jsonResponse(['error' => 'The username or email is already in use'], 400);
                break;
            default:
                $this->jsonResponse(['error' => 'A database error occurred'], 500);
                break;
        }
    }


    public function getEventById(int $id): void {
        $event = $this->eventService->getEventById($id);

        if ($event) {
            $this->jsonResponse($event->toArray());
        } else {
            $this->jsonResponse(['error' => 'Event not found'], 404);
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
                echo json_encode($data);
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
?>