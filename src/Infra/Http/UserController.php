<?php

namespace App\Infra\Http;

use App\Core\UseCases\FindUserByIdUseCase;
use App\Core\UseCases\ListUsersUseCase;
use App\Core\UseCases\RegisterUserUseCase;
use App\Infra\Http\Request;
use App\Infra\Http\Response;
use Psr\Log\LoggerInterface;

class UserController
{
    private $registerUserUseCase;
    private $listUsersUseCase;
    private $findUserByIdUseCase;
    private $logger;

    public function __construct(
        RegisterUserUseCase $registerUserUseCase,
        ListUsersUseCase $listUsersUseCase,
        FindUserByIdUseCase $findUserByIdUseCase,
        LoggerInterface $logger
    ) {
        $this->registerUserUseCase = $registerUserUseCase;
        $this->listUsersUseCase = $listUsersUseCase;
        $this->findUserByIdUseCase = $findUserByIdUseCase;
        $this->logger = $logger;
    }

    public function register(Request $request): void
    {
        try {
            $data = $request->getData();
            $this->registerUserUseCase->execute($data['name'], $data['email'], $data['password']);
            Response::success('Usuário cadastrado com sucesso!');
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            Response::error('Erro interno do servidor', 500);
        }
    }

    public function list(): void
    {
        try {
            $users = $this->listUsersUseCase->execute();
            $this->logger->info('Usuários listados com sucesso!');
            Response::success('Usuários listados com sucesso!', $users);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            Response::error('Erro interno do servidor', 500);
        }
    }

    public function findById($id): void
    {
        try {
            $user = $this->findUserByIdUseCase->execute($id);
            Response::success('Usuário encontrado com sucesso!', $user);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            Response::error('Erro interno do servidor', 500);
        }
    }
}
