<?php
namespace Gb\Php2\Articles;
use Gb\Php2\Users\User;

class Article 
{
    public ?int $id;
    public User $idAutor;
    public ?string $title;
    public ?string $text;

    public function __construct(int $id = null, string $title = null, string $text = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->text = $text;
    }

    public function __toString(): string
    {
        return $this->getTitle(). ' ' .$this->getText();
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