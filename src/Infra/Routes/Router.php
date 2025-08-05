<?php

namespace App\Infra\Routes;

use App\Infra\Http\Request;
use App\Infra\Http\UserController;
use App\Infra\Persistence\InMemoryUserRepository;
use App\Core\UseCases\RegisterUserUseCase;
use App\Core\UseCases\ListUsersUseCase;
use App\Core\UseCases\FindUserByIdUseCase;
use App\Infra\Http\Response;

class Router
{
    private Request $request;
    private UserController $userController;

    public function __construct()
    {
        $this->request = new Request();
        $this->initializeControllers();
    }

    private function initializeControllers(): void
    {
        $userRepository = new InMemoryUserRepository();
        $registerUserUseCase = new RegisterUserUseCase($userRepository);
        $listUsersUseCase = new ListUsersUseCase($userRepository);
        $findUserByIdUseCase = new FindUserByIdUseCase($userRepository);

        $this->userController = new UserController(
            $registerUserUseCase,
            $listUsersUseCase,
            $findUserByIdUseCase
        );
    }

    public function dispatch(): void
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        // Verificar se é uma rota da API
        if (strpos($uri, '/api') !== 0) {
            Response::error('Not Found', 404);
        }

        $route = substr($uri, 4); // Remove '/api' do início

        // Rota raiz
        if ($route === '/' || $route === '') {
            Response::success('Bem-vindo à API SpinWin');
            return;
        }

        // Rotas de usuários
        if (strpos($route, '/users') === 0) {
            $this->handleUserRoutes($route, $method);
            return;
        }

        Response::error('Rota não encontrada', 404);
    }

    private function handleUserRoutes(string $route, string $method): void
    {
        // POST /api/users - Criar usuário
        if ($route === '/users' && $method === 'POST') {
            $this->userController->register($this->request->getData());
            return;
        }

        // GET /api/users - Listar usuários
        if ($route === '/users' && $method === 'GET') {
            $this->userController->list();
            return;
        }

        // GET /api/users/{id} - Buscar usuário por ID
        if (preg_match('/^\/users\/(.+)$/', $route, $matches) && $method === 'GET') {
            $userId = $matches[1];
            $this->userController->findById($userId);
            return;
        }

        Response::error('Método não permitido', 405);
    }
}
