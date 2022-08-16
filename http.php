<?php
require_once __DIR__ . '/vendor/autoload.php';

use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\Actions\User\CreateUser;
use Gb\Php2\http\Request;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\http\SuccessfulResponse;
use Gb\Php2\http\Actions\User\FindByUsername;
use Gb\Php2\Repositories\AddToSqlitePostRepositories;
use Gb\Php2\Repositories\AddToSqliteUsersRepositories;


// Создаём объект запроса из суперглобальных переменных
$request = new Request($_GET,
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
        '/OpenServer/domains/Project_PHP_2/http.php/users/show/' => new FindByUsername(
            new AddToSqliteUsersRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'POST' => [
        '/OpenServer/domains/Project_PHP_2/http.php/users/create' => new CreateUser(
            new AddToSqliteUsersRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],

];


// Ищем маршрут среди маршрутов для этого метода
if (!array_key_exists($path, $routes[$method])) {
    (new ErrorResponse('Not found'))->send();
    return;
}

// Выбираем найденное действие
$action = $routes[$method][$path];

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