<?php

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use PHPUnit\Framework\TestCase;
use Gb\Php2\UnitTests\DummyLogger;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Repositories\SqliteUsersRepositories;


class SqliteUsersRepositoryTest extends TestCase
{
    public function  testItThrowsAnExceptionWhenUserNotFound(): void
    {
        $connectionMock = $this->createStub(PDO::class);
        $statementStub = $this->createStub(PDOStatement::class);
        $statementStub->method('fetch')->willReturn(false);
        $connectionMock->method('prepare')->willReturn($statementStub);

        $repository = new SqliteUsersRepositories($connectionMock, new DummyLogger());
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('Cannot find user: Ivan');
        $repository->getByUsername('Ivan');
    }

    public function testItSavesUserToDatabase(): void
    {
        $connectionStub = $this->createStub(PDO::class);
        $statementMock = $this->createMock(PDOStatement::class);

        $statementMock
            ->expects($this->once())
            ->method('execute')
            ->with([
                ':uuid' => 'f5cb51be-22ef-4c43-8ea3-171e91c6a00b',
                ':username' => 'ivan123',
                ':first_name' => 'Ivan',
                ':last_name' => 'Nikitin',
                ':password' => 'some_password',
            ]);

        $connectionStub->method('prepare')->willReturn($statementMock);

        $repository = new SqliteUsersRepositories($connectionStub, new DummyLogger());
        $repository->save(
            new User(
                new UUID('f5cb51be-22ef-4c43-8ea3-171e91c6a00b'),
                'ivan123',
                'Ivan',
                'Nikitin',
                'some_password'
            )
        );
    }
}
