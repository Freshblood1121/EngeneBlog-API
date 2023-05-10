<?php

// Подключаем автозагрузчик Composer

use Dotenv\Dotenv;
use Faker\Provider\Lorem;
use Faker\Provider\ru_RU\Internet;
use Faker\Provider\ru_RU\Person;
use Faker\Provider\ru_RU\Text;
use GeekBrains\LevelTwo\Blog\Container\DIContainer;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\SqliteAuthTokensRepository;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\PostsRepositoryInterface;
use GeekBrains\LevelTwo\Blog\Repositories\PostsRepository\SqlitePostsRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\SqliteUsersRepository;
use GeekBrains\LevelTwo\Blog\Repositories\UsersRepository\UsersRepositoryInterface;
use GeekBrains\LevelTwo\Http\Auth\BearerTokenAuthentication;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthentication;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\LevelTwo\Http\Auth\TokenAuthenticationInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Faker\Generator;

require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения из файла .env
Dotenv::createImmutable(__DIR__)->safeLoad();

// Создаём объект контейнера ..
$container = new DIContainer();
// .. и настраиваем его:
// 1. подключение к БД
$container->bind(
    PDO::class,
    new PDO('sqlite:' . __DIR__ . '/' . $_ENV['SQLITE_DB_PATH'])
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
// 4. репозиторий токенов
$container->bind(
    AuthTokensRepositoryInterface::class,
    SqliteAuthTokensRepository::class
);
$container->bind(
    PasswordAuthenticationInterface::class,
PasswordAuthentication::class
);
$container->bind(
    TokenAuthenticationInterface::class,
    BearerTokenAuthentication::class
);

// Выносим объект логгера в переменную
$logger = (new Logger('blog'));

if ('yes' === $_ENV['LOG_TO_FILES'])
{
    $logger->pushHandler(new StreamHandler(
        __DIR__ . '/logs/blog.log'
    ))->pushHandler(new StreamHandler(
            __DIR__ . '/logs/blog.error.log',
            level: Logger::ERROR,
            bubble: false,
        ));
}
// Включаем логирование в консоль,
// если переменная окружения LOG_TO_CONSOLE содержит значение 'yes'
if ('yes' === $_SERVER['LOG_TO_CONSOLE'])
{
    $logger->pushHandler(new StreamHandler("php://stdout"));
}

$container->bind(LoggerInterface::class, $logger);

// Создаём объект генератора тестовых данных
$faker = new Generator();

// Инициализируем необходимые нам виды данных
$faker->addProvider(new Person($faker));
$faker->addProvider(new Text($faker));
$faker->addProvider(new Internet($faker));
$faker->addProvider(new Lorem($faker));

// Добавляем генератор тестовых данных в контейнер внедрения зависимостей
$container->bind(Generator::class, $faker);

// Возвращаем объект контейнера
return $container;