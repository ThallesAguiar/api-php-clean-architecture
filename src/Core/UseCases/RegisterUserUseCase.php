<?php

namespace App\Core\UseCases;

use App\Application\Interfaces\UserRepositoryInterface;
use App\Core\Entities\User;

class RegisterUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $name, string $email, string $password): User
    {
        // Verificar se o email já existe
        if ($this->userRepository->emailExists($email)) {
            throw new \InvalidArgumentException('Email já está em uso');
        }

        // Criar usuário com senha criptografada
        $user = new User(
            $name,
            $email,
            password_hash($password, PASSWORD_BCRYPT)
        );

        // Salvar usuário
        $this->userRepository->save($user);

        return $user;
    }
}
