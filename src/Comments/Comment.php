<?php
namespace Gb\Php2\Comments;

use Gb\Php2\Users\User;
use Gb\Php2\Articles\Article;

class Comment 
{
    public ?int $id;
    public User $idAutor;
    public Article $idArticle;
    public ?string $text;

    public function __construct(int $id = null, string $text = null)
    {
        $this->id = $id;
        $this->text = $text;
    }

    
    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getIdAutor(): User
    {
        return $this->idAutor;
    }

    public function setIdAutor($idAutor)
    {
        $this->idAutor = $idAutor;

        return $this;
    }

    public function getIdArticle(): Article
    {
        return $this->idArticle;
    }

    public function setIdArticle($idArticle)
    {
        $this->idArticle = $idArticle;

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