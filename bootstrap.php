<?php

use Dotenv\Dotenv;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Gb\Php2\Blog\Container\DIContainer;
use Gb\Php2\http\Auth\PasswordAuthentication;
use Gb\Php2\http\Auth\AuthenticationInterface;
use Gb\Php2\http\Auth\IdentificationInterface;
use Gb\Php2\http\Auth\BearerTokenAuthentication;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Gb\Php2\Repositories\SqlitePostRepositories;
use Gb\Php2\http\Auth\JsonBodyUuidIdentification;
use Gb\Php2\Repositories\SqliteUsersRepositories;
use Gb\Php2\http\Auth\TokenAuthenticationInterface;
use Gb\Php2\Interfaces\CommentsRepositoryInterface;
use Gb\Php2\Repositories\SqliteCommentRepositories;
use Gb\Php2\Interfaces\AuthTokensRepositoryInterface;
use Gb\Php2\Repositories\SqlitePostLikesRepositories;
use Gb\Php2\http\Auth\PasswordAuthenticationInterface;
use Gb\Php2\Interfaces\LikesPostRepositoriesInterface;
use Gb\Php2\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' .  $_SERVER['SQLITE_DB_PATH'])
);

$logger = (new Logger('blog'));
// Включаем логирование в файлы,
// если переменная окружения LOG_TO_FILES
// содержит значение 'yes'
if ('yes' === $_SERVER['LOG_TO_FILES']) {
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}
// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE
// содержит значение 'yes'
if ('yes' === $_SERVER['LOG_TO_CONSOLE']) {
    $logger
        ->pushHandler(
            new StreamHandler("php://stdout")
        );
}
$container->bind(
    LoggerInterface::class,
    $logger
);

$container->bind(
    PasswordAuthenticationInterface::class,
    PasswordAuthentication::class
);

$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);

$container->bind(
    IdentificationInterface::class,
    JsonBodyUuidIdentification::class
);

$container->bind(
    AuthenticationInterface::class,
    PasswordAuthentication::class
);    
    
// 2. репозиторий статей
$container->bind(
    PostsRepositoryInterface::class,
    SqlitePostRepositories::class
);
// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepositories::class
);
$container->bind(
    LikesPostRepositoriesInterface::class,
    SqlitePostLikesRepositories::class
);

$container->bind(
    CommentsRepositoryInterface::class,
    SqliteCommentRepositories::class
);
// Возвращаем объект контейнера
return $container;
