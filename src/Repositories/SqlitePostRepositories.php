<?php

namespace Gb\Php2\Repositories;

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Psr\Log\LoggerInterface;
use Gb\Php2\Exeptions\PostNotFoundException;
use Gb\Php2\Exeptions\PostsRepositoryException;
use Gb\Php2\Interfaces\PostsRepositoryInterface;

class SqlitePostRepositories implements PostsRepositoryInterface
{
    private \PDO $connection;
    private LoggerInterface $logger;

    public function __construct(\PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(Post $post): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO posts (uuid, autor_uuid, title, text)
            VALUES (:uuid, :autor_uuid, :title, :text)'
        );

        // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$post->getUuid(),
            ':autor_uuid' => $post->getUuidAutor(),
            ':title' => $post->getTitle(),
            ':text' => $post->getText(),
        ]);

        $this->logger->info("Post created: {$post->getUuid()}");
    }

    public function get(UUID $uuid): Post
    {
        $statement = $this->connection->prepare(
            'SELECT posts.uuid as post_uuid,
                posts.title as post_title,
                posts.text as post_text,
                users.uuid as users_uuid,
                users.username as users_username, 
                users.first_name as users_first_name,
                users.last_name as users_last_name
            FROM posts LEFT JOIN users
            ON posts.autor_uuid = users.uuid
            WHERE posts.uuid = :uuid'
        );
        $statement->execute([
            ':uuid' => (string)$uuid,
        ]);

        return $this->getPost($statement, $uuid);
    }

    public function deletePostByTitle(string $title)
    {
        $statement = $this->connection->prepare(
            'DELETE FROM posts WHERE title = :title'
        );
        $statement->execute([':title' => $title]);
    }

    public function delete(UUID $uuid): void
    {
        try {
            $statement = $this->connection->prepare(
                'DELETE FROM posts WHERE uuid = ?'
            );
            $statement->execute([(string)$uuid]);
        } catch (\PDOException $e) {
            throw new PostsRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }

    public function getPostByTitle(string $title): Post
    {
        $statement = $this->connection->prepare(
            'SELECT posts.uuid as post_uuid,
                posts.title as post_title,
                posts.text as post_text,
                users.uuid as users_uuid,
                users.username as users_username, 
                users.first_name as users_first_name,
                users.last_name as users_last_name
            FROM posts LEFT JOIN users
            ON posts.autor_uuid = users.uuid
            WHERE title = :title'
            // 'SELECT * FROM posts WHERE title = :title'
        );

        $statement->execute([':title' => $title]);

        return $this->getPost($statement, $title);
    }

    private function getPost(\PDOStatement $statement, string $title): Post
    {

        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            $message = "No such header : $title";

            $this->logger->warning($message);
            throw new PostNotFoundException($message);
        }

        $user = new User(
            new UUID($result['users_uuid']),
            $result['users_username'],
            $result['users_first_name'],
            $result['users_last_name']
        );

        return new Post(
            new UUID($result['post_uuid']),
            $user,
            $result['post_title'],
            $result['post_text']
        );
    }
}
