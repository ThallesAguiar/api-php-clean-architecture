<?php

namespace App\Application\Providers;

use App\Core\UseCases\FindUserByIdUseCase;
use App\Core\UseCases\ListUsersUseCase;
use App\Core\UseCases\RegisterUserUseCase;
use App\Application\Interfaces\UserRepositoryInterface;
use App\Infra\DI\Container;
use App\Infra\Logger\Logger;
use App\Infra\Persistence\InMemoryUserRepository;
use Psr\Log\LoggerInterface;

class AppServiceProvider
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function register()
    {
        $this->container->singleton(UserRepositoryInterface::class, InMemoryUserRepository::class);

        $this->container->singleton(LoggerInterface::class, function () {
            return Logger::createLogger();
        });

        $this->container->bind(RegisterUserUseCase::class, function ($container) {
            return new RegisterUserUseCase(
                $container->make(UserRepositoryInterface::class)
            );
        });

        $this->container->bind(ListUsersUseCase::class, function ($container) {
            return new ListUsersUseCase(
                $container->make(UserRepositoryInterface::class)
            );
        });

        $this->container->bind(FindUserByIdUseCase::class, function ($container) {
            return new FindUserByIdUseCase(
                $container->make(UserRepositoryInterface::class)
            );
        });
    }
}
