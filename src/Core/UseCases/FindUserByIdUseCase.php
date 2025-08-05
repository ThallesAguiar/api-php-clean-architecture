<?php

namespace App\Core\UseCases;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Core\Entities\User;

class FindUserByIdUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $id): ?User
    {
        return $this->userRepository->findById($id);
    }
} 