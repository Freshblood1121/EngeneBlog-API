<?php

namespace GeekBrains\LevelTwo\Blog\Container;

use Faker\Container\ContainerInterface;
use GeekBrains\LevelTwo\Blog\Exceptions\NotFoundException;
use ReflectionClass;

class DIContainer implements ContainerInterface
{
    // Массив правил создания объектов
    private array $resolvers = [];

    // Метод для добавления правил
    public function bind(string $type, $resolver): void
    {
        $this->resolvers[$type] = $resolver;
    }
    /**
     * @throws NotFoundException
     */
    public function get(string $type): object
    {
        if (array_key_exists($type, $this->resolvers)) {
            $typeToCreate = $this->resolvers[$type];
    // Если в контейнере для запрашиваемого типа
    // уже есть готовый объект — возвращаем его
            if (is_object($typeToCreate)) {
                return $typeToCreate;
            }
            return $this->get($typeToCreate);
        }
        if (!class_exists($type)) {
            throw new NotFoundException("Cannot resolve type: $type");
        }
    // Создаём объект рефлексии для запрашиваемого класса
        $reflectionClass = new ReflectionClass($type);
    // Исследуем конструктор класса
        $constructor = $reflectionClass->getConstructor();
    // Если конструктора нет - просто создаём объект нужного класса
        if (null === $constructor) {
            return new $type();
        }
    // В этот массив мы будем собирать объекты зависимостей класса
        $parameters = [];
    // Проходим по всем параметрам конструктора (зависимостям класса)
        foreach ($constructor->getParameters() as $parameter) {
    // Узнаем тип параметра конструктора (тип зависимости)
            $parameterType = $parameter->getType()->getName();
    // Получаем объект зависимости из контейнера
            $parameters[] = $this->get($parameterType);
        }
    // Создаём объект нужного нам типа с параметрами
        return new $type(...$parameters);
    }
    public function has(string $type): bool
    {
    // Здесь мы просто пытаемся создать
    // объект требуемого типа
        try {
            $this->get($type);
        } catch (NotFoundException $e) {
    // Возвращаем false, если объект не создан...
            return false;
        }
    // и true, если создан
        return true;
    }
}