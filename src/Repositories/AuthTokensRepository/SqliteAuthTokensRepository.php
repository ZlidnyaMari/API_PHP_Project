<?php

namespace Gb\Php2\Repositories\AuthTokensRepository;

use DateTimeImmutable;
use DateTimeInterface;
use Gb\Php2\Blog\UUID;
use Gb\Php2\Blog\AuthToken;
use Gb\Php2\Exeptions\AuthTokenNotFoundException;
use Gb\Php2\Exeptions\AuthTokensRepositoryException;
use Gb\Php2\Interfaces\AuthTokensRepositoryInterface;

class SqliteAuthTokensRepository implements AuthTokensRepositoryInterface
{
    private \PDO $connection;

    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    // Метод сохранения токена
    public function save(AuthToken $authToken): void
    {
        $query = <<<'SQL'
                    INSERT INTO tokens (
                    token,
                    user_uuid,
                    expires_on
                    ) VALUES (
                    :token,
                    :user_uuid,
                    :expires_on
                    )
                    ON CONFLICT (token) DO UPDATE SET
                    expires_on = :expires_on
                    SQL;

        try {
            $statement = $this->connection->prepare($query);
            
            $statement->execute([
                ':token' => (string)$authToken->token(),
                ':user_uuid' => (string)$authToken->userUuid(),
                ':expires_on' => $authToken->expiresOn()
                    ->format(DateTimeInterface::ATOM),
            ]);
        } catch (\PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
    }
    // Метод получения токена
    public function get(string $token): AuthToken
    {
        try {
            $statement = $this->connection->prepare(
                'SELECT * FROM tokens WHERE token = ?'
            );
            $statement->execute([$token]);
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                (int)$e->getCode(),
                $e
            );
        }
        if (false === $result) {
            throw new AuthTokenNotFoundException("Cannot find token: $token");
        }
        try {
            return new AuthToken(
                $result['token'],
                new UUID($result['user_uuid']),
                new DateTimeImmutable($result['expires_on'])
            );
        } catch (\Exception $e) {
            throw new AuthTokensRepositoryException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}
