<?php
namespace Gb\Php2\Repositories;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\Exeptions\UserNotFoundException;

Class AddToSqliteUsersRepositories 
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public function save(User $user): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name)
            VALUES (:uuid, :username, :first_name, :last_name)'
        );
    
    // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username' => $user->getUser_name(),
            ':first_name' => $user->getfirst_name(),
            ':last_name' => $user->getLast_name(),
        ]);
    }

    public function getByUsername(string $user_name): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE username = :username'
        );

        $statement->execute([':username' => $user_name,]);

        return $this->getUser($statement, $user_name);
    }

    private function getUser(\PDOStatement $statement, string $user_name): User
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            throw new UserNotFoundException(
                "Cannot find user: $user_name"
            );
        }
        return new User(
            new UUID($result['uuid']),
                $result['username'],
                $result['first_name'], 
                $result['last_name']
        );
    }
}