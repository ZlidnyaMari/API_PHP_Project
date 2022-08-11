<?php
namespace Gb\Php2\Blog;
use Gb\Php2\Exeptions\InvalidArgumentException;

class UUID
{
    private string $uuidString;

    public function __construct(string $uuidString)      
    {   
        $this->uuidString = $uuidString; 

        if (!uuid_is_valid($uuidString)) {
            throw new InvalidArgumentException(
                "Malformed UUID: $this->uuidString"
            );
        }
    }

    public static function random(): self
    {
        return new self(uuid_create(UUID_TYPE_RANDOM));
    }

    public function __toString(): string
    {
        return $this->uuidString;
    }
}