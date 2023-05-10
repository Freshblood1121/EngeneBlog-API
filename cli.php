<?php

use GeekBrains\LevelTwo\Blog\Commands\FakeData\PopulateDB;
use GeekBrains\LevelTwo\Blog\Commands\Posts\DeletePost;
use GeekBrains\LevelTwo\Blog\Commands\Users\CreateUser;
use GeekBrains\LevelTwo\Blog\Commands\Users\UpdateUser;
use Symfony\Component\Console\Application;

$container = require __DIR__ . '/bootstrap.php';
// Создаём объект приложения
$application = new Application();
// Перечисляем классы команд

$commandsClasses = [
    CreateUser::class, //Создание пользователя
    DeletePost::class, //Удаление поста
    UpdateUser::class, //Обновление имени и фамилии пользователя
    PopulateDB::class, //Заполнение базы fake-данными
];

foreach ($commandsClasses as $commandClass) {
// Посредством контейнера
// создаём объект команды
    $command = $container->get($commandClass);
// Добавляем команду к приложению
    $application->add($command);
}
// Запускаем приложение
$application->run();