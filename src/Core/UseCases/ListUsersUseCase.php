<?php

namespace App\Core\UseCases;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Core\Entities\User;

class ListUsersUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(): array
    {
        $users = $this->userRepository->findAll();
        
        return array_map(function (User $user) {
            return $user->toArray();
        }, $users);
    }
} 