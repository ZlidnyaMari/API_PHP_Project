<?php
namespace Gb\Php2\Users;

class User 
{
    public ?int $id;
    public ?string $name;
    public ?string $surname;

    public function __construct(int $id = null, string $name = null, string $surname = null) {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
    }

    public function __toString(): string
    {
        return $this->getName(). ' ' .$this->getSurname(); 
    }
    
    public function getId():int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }
}