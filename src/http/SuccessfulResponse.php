<?php

namespace Gb\Php2\http;

// Класс успешного ответа
class SuccessfulResponse extends Response
{
    protected const SUCCESS = true;
    // Успешный ответ содержит массив с данными,
    // по умолчанию - пустой
    private array $data = [];
    public function __construct(array $data) 
    {
        $this->data = $data;
    }
    // Реализация абстрактного метода
    // родительского класса
    protected function payload(): array
    {
        return ['data' => $this->data];
    }
}
