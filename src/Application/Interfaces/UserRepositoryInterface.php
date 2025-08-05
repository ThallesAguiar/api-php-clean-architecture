<?php

namespace App\Application\Interfaces;

use App\Core\Entities\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;
    public function findById(string $id): ?User;
    public function findByEmail(string $email): ?User;
    public function findAll(): array;
    public function update(User $user): void;
    public function delete(string $id): void;
    public function emailExists(string $email): bool;
}