<?php

use Gb\Php2\Blog\Container\DIContainer;
use Gb\Php2\Interfaces\LikesPostRepositoriesInterface;
use Gb\Php2\Interfaces\PostsRepositoryInterface;
use Gb\Php2\Interfaces\UsersRepositoryInterface;
use Gb\Php2\Repositories\SqlitePostLikesRepositories;
use Gb\Php2\Repositories\SqlitePostRepositories;
use Gb\Php2\Repositories\SqliteUsersRepositories;

// Подключаем автозагрузчик Composer
require_once __DIR__ . '/vendor/autoload.php';
// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/blog.sqlite')
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
// Возвращаем объект контейнера
return $container;
