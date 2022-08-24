<?php
use Psr\Log\LoggerInterface;
use Gb\Php2\Blog\Commands\Arguments;
use Gb\Php2\Blog\Commands\CreateUserCommand;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';

$logger = $container->get(LoggerInterface::class);

try {
// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);
$command->handle(Arguments::fromArgv($argv));

} catch (\Exception $exeption) {
    $logger->error($exeption->getMessage(), ['exception' => $exeption]);
}
