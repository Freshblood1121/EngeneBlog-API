<?php

use GeekBrains\LevelTwo\Blog\Commands\Arguments;
use GeekBrains\LevelTwo\Blog\Commands\CreateUserCommand;
use GeekBrains\LevelTwo\Blog\Exceptions\AppException;
use Psr\Log\LoggerInterface;

// Подключаем файл bootstrap.php
// и получаем настроенный контейнер
$container = require __DIR__ . '/bootstrap.php';
// При помощи контейнера создаём команду
$command = $container->get(CreateUserCommand::class);
// Получаем объект логгера из контейнера
$logger = $container->get(LoggerInterface::class);

try {
    $command->handle(Arguments::fromArgv($argv));
} catch (AppException $e) {
//    echo "{$e->getMessage()}\n";
// Уровень логирования – ERROR
    $logger->error($e->getMessage(), ['exception' => $e]);
}