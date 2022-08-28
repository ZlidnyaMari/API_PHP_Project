<?php

namespace Gb\Php2\Blog;

class User
{
    private UUID $uuid;
    private ?string $user_name;
    private ?string $first_name;
    private ?string $last_name;
    private ?string $hashedPassword;

    public function __construct(
        UUID $uuid,
        string $user_name = null,
        string $first_name = null,
        string $last_name = null,
        string $hashedPassword = null
    ) {

        $this->uuid = $uuid;
        $this->user_name = $user_name;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->hashedPassword = $hashedPassword;
    }

    public function __toString(): string
    {
        return $this->getFirst_name() . ' ' . $this->getLast_name();
    }

    public function getUuid(): UUID
    {
        return $this->uuid;
    }

    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getUser_name(): string
    {
        return $this->user_name;
    }

    public function setUser_name($user_name)
    {
        $this->user_name = $user_name;

        return $this;
    }

    public function getLast_name(): string
    {
        return $this->last_name;
    }

    public function setLast_name($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * Get the value of first_name
     */
    public function getFirst_name(): string
    {
        return $this->first_name;
    }

    public function setFirst_name($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }

    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }

    // Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }

    public static function createFrom(
        string $username,
        string $first_name,
        string $last_name,
        string $password
    ): self {

        $uuid = UUID::random();

        return new self(
            $uuid,
            $username,
            $first_name,
            $last_name,
            self::hash($password, $uuid)
        );
    }
}
