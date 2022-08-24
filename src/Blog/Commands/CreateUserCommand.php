<?php

namespace Gb\Php2\Blog\Commands;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Commands\Arguments;
use Gb\Php2\Exeptions\CommandException;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Psr\Log\LoggerInterface;

class CreateUserCommand
{
    private UsersRepositoryInterface $usersRepository;
    // Команда зависит от контракта репозитория пользователей,
    // а не от конкретной реализации
    private LoggerInterface $logger;

    public function __construct(UsersRepositoryInterface $usersRepository, LoggerInterface $logger)
    {
        $this->usersRepository = $usersRepository;
        $this->logger = $logger;
    }

    public function handle(Arguments $arguments): void
    {
        $this->logger->info("Create user command started");

        $username = $arguments->get('username');

        // Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
            $this->logger->warning("User already exists: $username");
            // Вместо выбрасывания исключения просто выходим из функции

            // Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }

        $uuid = UUID::random();

        // Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            $uuid,
            $username,
            $arguments->get('first_name'),
            $arguments->get('last_name')
        ));

        $this->logger->info("User created: $uuid");
    }

    private function userExists(string $username): bool
    {
        try {
            // Пытаемся получить пользователя из репозитория
            $this->usersRepository->getByUsername($username);
        } catch (UserNotFoundException) {
            return false;
        }
        return true;
    }
}
