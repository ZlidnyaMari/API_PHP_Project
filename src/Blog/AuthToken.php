<?php

namespace Gb\Php2\Blog;

use DateTimeImmutable;

class AuthToken
{
    // Строка токена
    private string $token;
    // UUID пользователя
    private UUID $userUuid;
    // Срок годности
    private DateTimeImmutable $expiresOn;

    public function __construct(string $token, UUID $userUuid, DateTimeImmutable $expiresOn) 
    {
        $this->token = $token;
        $this->userUuid = $userUuid;
        $this->expiresOn = $expiresOn;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function userUuid(): UUID
    {
        return $this->userUuid;
    }

    public function expiresOn(): DateTimeImmutable
    {
        return $this->expiresOn;
    }

    public function setExpiresOn(DateTimeImmutable $expiresOn)
    {
        $this->expiresOn = $expiresOn;

        return $this;
    }
}
