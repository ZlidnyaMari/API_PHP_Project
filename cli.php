<?php

use Psr\Log\LoggerInterface;
use Gb\Php2\Blog\Commands\User\CreateUser;
use Gb\Php2\Blog\Commands\User\UpdateUser;
use Symfony\Component\Console\Application;
use Gb\Php2\Blog\Commands\Posts\DeletePost;
use Gb\Php2\Blog\Commands\FakeData\PopulateDB;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд
$commandsClasses = [
    CreateUser::class,
    DeletePost::class,
    UpdateUser::class,
    PopulateDB::class,
];

foreach ($commandsClasses as $commandClass) {
    // Посредством контейнера
    // создаём объект команды
    $command = $container->get($commandClass);
    // Добавляем команду к приложению
    $application->add($command);
}

try {

    // Запускаем приложение
    $application->run();
    
    // // При помощи контейнера создаём команду
    // $command = $container->get(CreateUserCommand::class);
    // $command->handle(Arguments::fromArgv($argv));
} catch (\Exception $exeption) {
    $logger->error($exeption->getMessage(), ['exception' => $exeption]);
}
