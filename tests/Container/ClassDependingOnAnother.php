<?php
namespace Gb\Php2\UnitTests\Container;

use Gb\Php2\UnitTests\Container\SomeClassWithParameter;
use Gb\Php2\UnitTests\Container\SomeClassWithoutDependencies;

class ClassDependingOnAnother
{
    // Класс с двумя зависимостями
    private SomeClassWithoutDependencies $one;
    private SomeClassWithParameter $two;

    public function __construct(SomeClassWithoutDependencies $one, SomeClassWithParameter $two) 
    {
        $this->one = $one;
        $this->two = $two;
    }
}