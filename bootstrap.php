<?php

// Подключаем автозагрузчик Composer
use GeekBrains\LevelTwo\Blog\Container\DIContainer;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

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
    SqlitePostsRepository::class
);
// 3. репозиторий пользователей
$container->bind(
    UsersRepositoryInterface::class,
    SqliteUsersRepository::class
);
// Добавляем логгер в контейнер
$container->bind(
    LoggerInterface::class,
    (new Logger('blog'))
        ->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.log'
        ))
// Добавили новый обработчик:
        ->pushHandler(new StreamHandler(
// записывать в файл "blog.error.log"
            __DIR__ . '/logs/blog.error.log',
// события с уровнем ERROR и выше,
            level: Logger::ERROR,
// при этом событие не должно "всплывать"
            bubble: false,
        ))
);

// Возвращаем объект контейнера
return $container;
