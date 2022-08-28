<?php
namespace Gb\Php2\Repositories;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Psr\Log\LoggerInterface;
use Gb\Php2\Exeptions\UserNotFoundException;
use Gb\Php2\Interfaces\UsersRepositoryInterface;

Class SqliteUsersRepositories implements UsersRepositoryInterface
{
    private \PDO $connection;
    private LoggerInterface $logger;

    public function __construct(\PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(User $user): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO users (uuid, username, first_name, last_name, password)
            VALUES (:uuid, :username, :first_name, :last_name, :password)'
        );
    
    // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$user->getUuid(),
            ':username' => $user->getUser_name(),
            ':first_name' => $user->getFirst_name(),
            ':last_name' => $user->getLast_name(),
            ':password' => $user->hashedPassword(),
        ]);

        $this->logger->info("Post created: {$user->getUuid()}");
    }

    public function get(UUID $uuid): User
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getUser($statement, $uuid);
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
            $message = "Cannot find user: $user_name";

            $this->logger->warning($message);
            throw new UserNotFoundException($message);
        }
        return new User(
            new UUID($result['uuid']),
                $result['username'],
                $result['first_name'], 
                $result['last_name'],
                $result['password'],
        );
    }
}