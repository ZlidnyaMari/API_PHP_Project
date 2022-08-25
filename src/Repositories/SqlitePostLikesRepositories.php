<?php

namespace Gb\Php2\Repositories;

use Gb\Php2\Blog\Likes;
use Psr\Log\LoggerInterface;
use Gb\Php2\Exeptions\LikesLimitExeption;
use Gb\Php2\Exeptions\LikesNotFoundException;
use Gb\Php2\Interfaces\LikesPostRepositoriesInterface;

class SqlitePostLikesRepositories implements LikesPostRepositoriesInterface
{
    private \PDO $connection;
    private LoggerInterface $logger;

    public function __construct(\PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(Likes $like): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO likes (uuid, post_uuid, autor_uuid)
            VALUES (:uuid, :post_uuid, :autor_uuid)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$like->getUuid(),
            ':post_uuid' => $like->getUuidPostLikes(),
            ':autor_uuid' => $like->getUuidUserLikes()
        ]);

        $this->logger->info("Post created: {$like->getUuid()}");
    }

    public function getByPostUuid(string $uuid): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes WHERE post_uuid = :uuid'
        );

        $statement->execute([':uuid' => $uuid]);

        return $this->getUuid($statement, $uuid);
    }

    private function getUuid(\PDOStatement $statement, string $uuid): array
    {
        $result = $statement->fetchAll();

        if ($result === false) {
            $message = "No such like : $uuid";
            
            $this->logger->warning($message);
            throw new LikesNotFoundException($message);
        }
        return $result;
    }

    public function likesLimit($postUuid, $userUuid)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM likes 
                WHERE post_uuid = :post_uuid AND autor_uuid = :autor_uuid'
        );

        $statement->execute([
            ':post_uuid' => $postUuid,
            ':autor_uuid' => $userUuid
        ]);
        
        $rezult = $statement->fetch();
        
        if ($rezult) {
            throw new LikesLimitExeption(
                'This user has already liked'
            );
        }
    }
}
