<?php
namespace Gb\Php2\Repositories;

use Gb\Php2\Blog\Post;
use Gb\Php2\Blog\User;
use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\Comment;
use Psr\Log\LoggerInterface;
use Gb\Php2\Exeptions\CommentNotFoundException;
use Gb\Php2\Interfaces\CommentsRepositoryInterface;

class SqliteCommentRepositories  implements CommentsRepositoryInterface
{
    private \PDO $connection;
    private LoggerInterface $logger;


    public function __construct(\PDO $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    public function save(Comment $comment): void
    {
        // Подготавливаем запрос
        $statement = $this->connection->prepare(
            'INSERT INTO comments (uuid, post_uuid, autor_uuid, text)
            VALUES (:uuid, :post_uuid, :autor_uuid, :text)'
        );
    
    // Выполняем запрос с конкретными значениями
        $statement->execute([
            ':uuid' => (string)$comment->getUuid(),
            ':post_uuid'=> $comment->getUuidPost(),
            ':autor_uuid' => $comment->getUuidAutor(),
            ':text' => $comment->getText(),
        ]);

        $this->logger->info("Post created: {$comment->getUuid()}");
    }

    public function getCommentUuid(string $uuid): Comment
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM comments WHERE uuid = :uuid'
        );

        $statement->execute([':uuid' => $uuid]);

        return $this->getComment($statement, $uuid);
    }

    private function getComment(\PDOStatement $statement, string $uuid): Comment
    {
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if ($result === false) {
            $message = "No such comment : $uuid";

            $this->logger->warning($message);
            throw new CommentNotFoundException($message);
        }
        
        $user = new User(
            new UUID($result['uuid']),
                $result['username'],
                $result['first_name'], 
                $result['last_name']
        );

        $post = new Post(
            new UUID($result['uuid']),
                $user,
                $result['title'], 
                $result['text']
        );

        return new Comment(
            new UUID($result['uuid']),
                $user,
                $post, 
                $result['text']
        );
    }


}