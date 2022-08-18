<?php
require_once __DIR__ . '/vendor/autoload.php';


use Gb\Php2\http\Actions\Posts\DeletePostByTitle;
use Gb\Php2\http\Request;
use Gb\Php2\http\ErrorResponse;
use Gb\Php2\Exeptions\HttpException;
use Gb\Php2\http\Actions\User\CreateUser;
use Gb\Php2\http\Actions\Posts\CreatePosts;
use Gb\Php2\http\Actions\User\FindByUsername;
use Gb\Php2\http\Actions\Comment\CreateComment;
use Gb\Php2\Repositories\SqlitePostRepositories;
use Gb\Php2\Repositories\SqliteUsersRepositories;
use Gb\Php2\Repositories\SqliteCommentRepositories;


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
        '/OpenServer/domains/Project_PHP_2/http.php/users/show/' => new FindByUsername(
            new SqliteUsersRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
    ],
    'POST' => [
        '/OpenServer/domains/Project_PHP_2/http.php/users/create' => new CreateUser(
            new SqliteUsersRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/OpenServer/domains/Project_PHP_2/http.php/posts/create' => new CreatePosts(
            new SqlitePostRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteUsersRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        ),
        '/OpenServer/domains/Project_PHP_2/http.php/comment/create' => new CreateComment(
            new SqliteUsersRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqlitePostRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            ),
            new SqliteCommentRepositories(
                new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
            )
        )
    ],
    'DELETE' => [
        '/OpenServer/domains/Project_PHP_2/http.php/posts' => new DeletePostByTitle(
            new SqlitePostRepositories(
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
