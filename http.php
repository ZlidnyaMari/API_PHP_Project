<?php

use Gb\Php2\http\Request;
use Psr\Log\LoggerInterface;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\Actions\User\CreateUser;
use Gb\Php2\http\Actions\Likes\CreateLikes;
use Gb\Php2\http\Actions\Posts\CreatePosts;
use Gb\Php2\http\Actions\User\FindByUsername;
use Gb\Php2\http\Actions\Comment\CreateComment;
use Gb\Php2\http\Actions\Posts\DeletePostByTitle;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

// Создаём объект запроса из суперглобальных переменных
$request = new Request(
    $_GET,
    $_SERVER,
    file_get_contents('php://input')
);

$logger = $container->get(LoggerInterface::class);

try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException $e) {
    // Отправляем неудачный ответ,
    // если по какой-то причине
    // не можем получить путь
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    // Выходим из программы
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException $e) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
    $logger->warning($e->getMessage());
    (new ErrorResponse)->send();
    return;
}

$routes = [
    'GET' => [
        '/users/show/' => FindByUsername::class
    ],
    'POST' => [
        '/users/create' => CreateUser::class,
        '/posts/create' => CreatePosts::class,
        '/comment/create' => CreateComment::class,
        '/likes/create' => CreateLikes::class
    ],
    'DELETE' => [
        '/posts/delete' => DeletePostByTitle::class,
    ],
];

// Ищем маршрут среди маршрутов для этого метода
if (
    !array_key_exists($method, $routes)
    || !array_key_exists($path, $routes[$method])
) {
    // Логируем сообщение с уровнем NOTICE
    $message = "Route not found: $method $path";
    $logger->notice($message);
    (new ErrorResponse($message))->send();
    return;
}


// Выбираем найденное действие
$actionClassName = $routes[$method][$path];


try {
    // Пытаемся выполнить действие,
    // при этом результатом может быть
    // как успешный, так и неуспешный ответ
    $action = $container->get($actionClassName);
    $response = $action->handle($request);
    // Отправляем ответ
    $response->send();
} catch (Exception $e) {
    // Отправляем неудачный ответ,
    // если что-то пошло не так
    $logger->error($e->getMessage(), ['exception' => $e]);
    (new ErrorResponse($e->getMessage()))->send();
}
