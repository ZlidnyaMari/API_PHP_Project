<?php

namespace Gb\Php2\UnitTests\Command;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use PHPUnit\Framework\TestCase;
use Gb\Php2\UnitTests\DummyLogger;
use Gb\Php2\Blog\Commands\Arguments;
use Gb\Php2\Exeptions\CommandException;
use Gb\Php2\Blog\Commands\User\CreateUser;
use Gb\Php2\Blog\Commands\CreateUserCommand;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Repositories\DummyUsersRepository;
use Symfony\Component\Console\Input\ArrayInput;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Exception\RuntimeException;

class CreateUserCommandTest extends TestCase
{
    public function testItRequiresPassword(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No such argument: password');
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
            ]),
            new NullOutput()
        );
    }

    public function testItThrowsAnExceptionWhenUserAlreadyExists(): void
    {
        $command = new CreateUserCommand(
            new DummyUsersRepository(),
            new DummyLogger()
        );
        // Описываем тип ожидаемого исключения
        $this->expectException(CommandException::class);

        // и его сообщение
        $this->expectExceptionMessage('User already exists: Ivan');
        // Запускаем команду с аргументами
        $command->handle(new Arguments([
            'username' => 'Ivan',
            'password' => 'Ivan',
        ]));
    }

    // Функция возвращает объект типа UsersRepositoryInterface
    private function makeUsersRepository(): UsersRepositoryInterface
    {
        return new class implements UsersRepositoryInterface
        {
            public function save(User $user): void
            {
            }

            public function get(UUID $uuid): User
            {
                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User

            {
                throw new UserNotFoundException("Not found");
            }
        };
    }

    // Тест проверяет, что команда действительно требует фамилию пользователя
    public function testItRequiresLastName(): void
    {
        // Передаём в конструктор команды объект, возвращаемый нашей функцией
        $command = new CreateUser(
            $this->makeUsersRepository()
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No such argument: last_name');
        $command->run(
            // Передаём аргументы как ArrayInput,
            // а не Arguments
            // Сами аргументы не меняются
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
                'first_name' => 'Ivan',
            ]),
            // Передаём также объект,
            // реализующий контракт OutputInterface
            // Нам подойдёт реализация,
            // которая ничего не делает
            new NullOutput()
        );
    }

    public function testItRequiresFirstName(): void
    {
        $command = new CreateUser(
            $this->makeUsersRepository()
        );
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Not enough arguments (missing: "first_name, last_name").'
        );
        $command->run(
            new ArrayInput([
                'username' => 'Ivan',
                'password' => 'some_password',
            ]),
            new NullOutput()
        );
    }

    // Тест, проверяющий, что команда сохраняет пользователя в репозитории
    public function testItSavesUserToRepository(): void
    {
        // Создаём объект анонимного класса
        $usersRepository = new class implements UsersRepositoryInterface
        {
            // В этом свойстве мы храним информацию о том,
            // был ли вызван метод save
            private bool $called = false;

            public function save(User $user): void
            {
                // Запоминаем, что метод save был вызван
                $this->called = true;
            }

            public function get(UUID $uuid): User
            {

                throw new UserNotFoundException("Not found");
            }

            public function getByUsername(string $username): User
            {
                throw new UserNotFoundException("Not found");
            }
            // Этого метода нет в контракте UsersRepositoryInterface,
            // но ничто не мешает его добавить.
            // С помощью этого метода мы можем узнать,
            // был ли вызван метод save
            public function wasCalled(): bool
            {
                return $this->called;
            }
        };

        $command = new CreateUser(
            $usersRepository
        );

        // Запускаем команду
        $command->run(
            new ArrayInput([
            'username' => 'Ivan',
            'password' => 'some_password',
            'first_name' => 'Ivan',
            'last_name' => 'Nikitin',
            ]),
            new NullOutput()
            );

        $this->assertTrue($usersRepository->wasCalled());
    }

    // Передаём наш мок в команду

}
