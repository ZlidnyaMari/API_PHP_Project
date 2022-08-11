<?php
namespace Gb\Php2\Blog;

use Gb\Php2\Blog\User;
use Gb\Php2\Blog\Post;

class Comment 
{
    public UUID $uuid;
    public string $uuidAutor;
    public string $uuidPost;
    public ?string $text;

    public function __construct(UUID $uuid, User $user, Post $post, string $text = null)
    {
        $this->uuid = $uuid;
        $this->uuidAutor = $user->getUuid();
        $this->uuidPost = $post->getUuid();
        $this->text = $text;
    }

    public function __toString():string
    {
        return $this->getText();   
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

    public function getUuidAutor(): string
    {
        return $this->uuidAutor;
    }

    public function setUuidAutor($uuidAutor)
    {
        $this->uuidAutor = $uuidAutor;

        return $this;
    }

    public function getUuidPost(): string
    {
        return $this->uuidPost;
    }

    public function setUuidPost($uuidPost)
    {
        $this->uuidPost = $uuidPost;

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