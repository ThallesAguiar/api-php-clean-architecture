<?php

namespace App\Infra\Http;

use App\Core\UseCases\RegisterUserUseCase;
use App\Core\UseCases\ListUsersUseCase;
use App\Core\UseCases\FindUserByIdUseCase;
use App\Infra\Http\Response;

class UserController
{
    public function __construct(
        private RegisterUserUseCase $registerUserUseCase,
        private ListUsersUseCase $listUsersUseCase,
        private FindUserByIdUseCase $findUserByIdUseCase
    ) {}

    public function register(array $data): void
    {
        try {
            $user = $this->registerUserUseCase->execute(
                $data['name'] ?? '',
                $data['email'] ?? '',
                $data['password'] ?? ''
            );

            Response::success('Usuário criado com sucesso!', $user->toArray(), 201);
        } catch (\InvalidArgumentException $e) {
            Response::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            Response::error('Erro interno do servidor', 500);
        }
    }

    public function list(): void
    {
        try {
            $users = $this->listUsersUseCase->execute();
            Response::success('Usuários listados com sucesso!', $users);
        } catch (\Exception $e) {
            Response::error('Erro interno do servidor', 500);
        }
    }

    public function findById(string $id): void
    {
        try {
            $user = $this->findUserByIdUseCase->execute($id);
            
            if (!$user) {
                Response::error('Usuário não encontrado', 404);
                return;
            }

            Response::success('Usuário encontrado com sucesso!', $user->toArray());
        } catch (\Exception $e) {
            Response::error('Erro interno do servidor', 500);
        }
    }

    public function handle(array $data): void
    {
        $this->register($data);
    }
}
