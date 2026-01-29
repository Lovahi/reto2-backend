<?php

namespace App\Service;

use App\Repository\UserEventRepository;
use App\DTO\UserEventDTO;

class UserEventService {
    private UserEventRepository $UserEventRepository;
    private UserService $UserService;

    public function __construct(UserEventRepository $UserEventRepository, UserService $UserService) {
        $this->UserEventRepository = $UserEventRepository;
        $this->UserService = $UserService;
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
        return $this->UserEventRepository->signUpEvent($user->id, $eventId);
    }

    public function signDownUserEvent(int $userId, int $eventId): bool {
        $user = $this->UserService->getUserById($userId);
        if (!$user) throw new \Exception("User not found");
        return $this->UserEventRepository->signDownEvent($user->id, $eventId);
    }
}
