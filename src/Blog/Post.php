<?php
namespace Gb\Php2\Blog;
use Gb\Php2\Blog\User;

class Post 
{
    public UUID $uuid;
    public ?string $uuidAutor;
    public ?string $title;
    public ?string $text;

    public function __construct(UUID $uuid, User $user, string $title = null, string $text = null)
    {
        $this->uuid = $uuid;
        $this->uuidAutor = $user->getUuid();
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return $this->getTitle(). ' ' .$this->getText();
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

    public function getUuidAutor()
    {
        return $this->uuidAutor; 
    }

    public function setUuidAutor($uuidAutor)
    {
        $this->uuidAutor = $uuidAutor;

        return $this;
    }

    public function getTitle():string
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }
}