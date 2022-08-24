<?php
namespace Gb\Php2\Repositories;

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Psr\Log\LoggerInterface;
use Gb\Php2\Exeptions\PostNotFoundException;
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
            'SELECT * FROM posts WHERE uuid = :uuid'
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

    public function getPostByTitle(string $title): Post
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM posts WHERE title = :title'
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
            new UUID($result['uuid']),
                $result['username'],
                $result['first_name'], 
                $result['last_name']
        );

        return new Post(
            new UUID($result['uuid']),
                $user,
                $result['title'], 
                $result['text']
        );
    }
}