<?php

namespace App\Controller;

use App\Service\UserEventService;
use PDOException;
use Exception;
use App\Core\ApiResponseTrait;

class UserEventController {
    use ApiResponseTrait;
    
    private UserEventService $userEventService;

    public function __construct(UserEventService $userEventService) {
        $this->userEventService = $userEventService;
    }

    public function getUserEvents(int $userId): void  {
        $userEvents = $this->userEventService->getUserEvents($userId);
        $this->jsonResponse(array_map(fn($u) => $u->toArray(), $userEvents));
    }

    public function getEventsUsers(int $eventId): void {
        $eventsUsers = $this->userEventService->getEventsUsers($eventId);
        $this->jsonResponse(array_map(fn($u) => $u->toArray(), $eventsUsers));
    }
    
    public function signUp(int $eventId): void {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['error' => 'User not authenticated'], 401);
            return;
        }
        try {
            $this->userEventService->signUpUserEvent($userId, $eventId);
            $this->jsonResponse(['message' => 'User signed up for event successfully']);
        } catch (PDOException $e) {
            $this->handleDatabaseException($e);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function cancelSignup(int $eventId): void {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            $this->jsonResponse(['error' => 'User not authenticated'], 401);
            return;
        }
        try {
            $this->userEventService->signDownUserEvent($userId, $eventId);
            $this->jsonResponse(['message' => 'User unsubscribed from event successfully']);
        } catch (PDOException $e) {
            $this->handleDatabaseException($e);
        } catch (Exception $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}