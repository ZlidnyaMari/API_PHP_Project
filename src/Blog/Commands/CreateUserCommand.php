<?php
namespace Gb\Php2\Blog\Commands;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Commands\Arguments;
use Gb\Php2\Exeptions\CommandException;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Interfaces\UsersRepositoryInterface;

class CreateUserCommand
{
    private UsersRepositoryInterface $usersRepository;
// Команда зависит от контракта репозитория пользователей,
// а не от конкретной реализации
    public function __construct(UsersRepositoryInterface $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }

    public function handle(Arguments $arguments): void
    {
        $username = $arguments->get('username');

// Проверяем, существует ли пользователь в репозитории
        if ($this->userExists($username)) {
// Бросаем исключение, если пользователь уже существует
            throw new CommandException("User already exists: $username");
        }

// Сохраняем пользователя в репозиторий
        $this->usersRepository->save(new User(
            UUID::random(),
            $username,
            $arguments->get('first_name'), 
            $arguments->get('last_name')
        ));
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