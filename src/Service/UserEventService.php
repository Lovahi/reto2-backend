<?php

namespace App\Service;

use App\Repository\UserEventRepository;
use App\DTO\UserEventDTO;
use App\Service\EventService;

class UserEventService {
    private UserEventRepository $UserEventRepository;
    private UserService $UserService;
    private EventService $EventService;

    public function __construct(UserEventRepository $UserEventRepository, UserService $UserService, EventService $EventService) {
        $this->UserEventRepository = $UserEventRepository;
        $this->UserService = $UserService;
        $this->EventService = $EventService;
    }

    public function getUserEvents(int $id): array {
        $userEvent = $this->UserEventRepository->getUserEvents($id);
        if (!$userEvent) return [];

        return array_map(fn($userEvent) => new UserEventDTO(
            $userEvent->getUserId(),
            $userEvent->getEventId()
        ), $userEvent);
    }

    public function getEventsUsers(int $id): array {
        $userEvents = $this->UserEventRepository->getEventsUsers($id);
        return array_map(fn($userEvent) => new UserEventDTO(
            $userEvent->getUserId(),
            $userEvent->getEventId()
        ), $userEvents);
    }

    public function signUpUserEvent(int $userId, int $eventId): bool {
        $user = $this->UserService->getUserById($userId);
        if (!$user) throw new \Exception("User not found");
        
        if ($this->UserEventRepository->signUpEvent($userId, $eventId)) {
            return $this->EventService->decrementAvailablePlaces($eventId);
        }
        return false;
    }

    public function signDownUserEvent(int $userId, int $eventId): bool {
        $user = $this->UserService->getUserById($userId);
        if (!$user) throw new \Exception("User not found");
        
        if ($this->UserEventRepository->signDownEvent($userId, $eventId)) {
            return $this->EventService->incrementAvailablePlaces($eventId);
        }
        return false;
    }
}
