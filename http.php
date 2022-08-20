<?php
use Gb\Php2\http\Request;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\Actions\User\CreateUser;
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
try {
    // Пытаемся получить путь из запроса
    $path = $request->path();
} catch (HttpException) {
    // Отправляем неудачный ответ,
    // если по какой-то причине
    // не можем получить путь
    (new ErrorResponse)->send();
    // Выходим из программы
    return;
}

try {
    // Пытаемся получить HTTP-метод запроса
    $method = $request->method();
} catch (HttpException) {
    // Возвращаем неудачный ответ,
    // если по какой-то причине
    // не можем получить метод
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
        '/comment/create' => CreateComment::class
    ],
    'DELETE' => [
        '/posts/delete' => DeletePostByTitle::class
    ],
];

// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем найденное действие
$actionClassName = $routes[$method][$path];
$action = $container->get($actionClassName);

try {
    // Пытаемся выполнить действие,
    // при этом результатом может быть
    // как успешный, так и неуспешный ответ
    $response = $action->handle($request);
    // Отправляем ответ
    $response->send();
} catch (Exception $e) {
    // Отправляем неудачный ответ,
    // если что-то пошло не так
    (new ErrorResponse($e->getMessage()))->send();
}
