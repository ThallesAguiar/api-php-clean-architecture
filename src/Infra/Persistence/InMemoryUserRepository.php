<?php

namespace App\Infra\Persistence;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Core\Entities\User;

class InMemoryUserRepository implements UserRepositoryInterface
{
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function findById(string $id): ?User
    {
        return $this->users[$id] ?? null;
    }

    public function findByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }
        return null;
    }

    public function findAll(): array
    {
        return array_values($this->users);
    }

    public function update(User $user): void
    {
        if (isset($this->users[$user->getId()])) {
            $this->users[$user->getId()] = $user;
        }
    }

    public function delete(string $id): void
    {
        unset($this->users[$id]);
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    // Método auxiliar para debug
    public function all(): array
    {
        return $this->users;
    }
}
