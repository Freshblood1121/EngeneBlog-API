<?php

namespace GeekBrains\LevelTwo\Http;

use GeekBrains\LevelTwo\Blog\Exceptions\HttpException;
use GeekBrains\LevelTwo\Blog\Exceptions\JsonException;


class Request
{
    public function __construct(
        // аргумент, соответствующий суперглобальной переменной $_GET
        private array $get,
        // аргумент, соответствующий суперглобальной переменной $_SERVER
        private array $server,
        private string $body,

    ) {
    }

    /**
     * @throws HttpException
     */
    public function method(): string
    {
    // В суперглобальном массиве $_SERVER
    // HTTP-метод хранится под ключом REQUEST_METHOD
        if (!array_key_exists('REQUEST_METHOD', $this->server)) {
    // Если мы не можем получить метод - бросаем исключение
            throw new HttpException('Cannot get method from the request');
        }
        return $this->server['REQUEST_METHOD'];
    }

    // Метод для получения массива,
// сформированного из json-форматированного
// тела запроса
    /**
     * @throws HttpException
     */
    public function jsonBody(): array
    {
        try {
// Пытаемся декодировать json
            $data = json_decode(
                $this->body,
// Декодируем в ассоциативный массив
                associative: true,
// Бросаем исключение при ошибке
                flags: JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            throw new HttpException("Cannot decode json body");
        }
        if (!is_array($data)) {
            throw new HttpException("Not an array/object in json body");
        }
        return $data;
    }
// Метод для получения отдельного поля
// из json-форматированного тела запроса
    /**
     * @throws HttpException
     */
    public function jsonBodyField(string $field): mixed
    {
        $data = $this->jsonBody();
        if (!array_key_exists($field, $data)) {
            throw new HttpException("No such field: $field");
        }
        if (empty($data[$field])) {
            throw new HttpException("Empty field: $field");
        }
        return $data[$field];
    }


    // Метод для получения пути запроса
    // Напрмер, для http://example.com/some/page?x=1&y=acb
    // путём будет строка '/some/page'
    /**
     * @throws HttpException
     */
    public function path(): string
    {
        // В суперглобальном массиве $_SERVER
        // значение URI хранится под ключом REQUEST_URI
        if (!array_key_exists('REQUEST_URI', $this->server)) {
        // Если мы не можем получить URI - бросаем исключение
            throw new HttpException('Cannot get path from the request');
        }

        // Используем встроенную в PHP функцию parse_url
        $components = parse_url($this->server['REQUEST_URI']);
        if (!is_array($components) || !array_key_exists('path', $components)) {
        // Если мы не можем получить путь - бросаем исключение
            throw new HttpException('Cannot get path from the request');
        }
        return $components['path'];
    }

    // Метод для получения значения
// определённого параметра строки запроса
// Напрмер, для http://example.com/some/page?x=1&y=acb
// значением параметра x будет строка '1'
    /**
     * @throws HttpException
     */
    public function query(string $param): string
    {
        if (!array_key_exists($param, $this->get)) {
// Если нет такого параметра в запросе - бросаем исключение
            throw new HttpException(
                "No such query param in the request: $param"
            );

        }
        $value = trim($this->get[$param]);
        if (empty($value)) {
// Если значение параметра пусто - бросаем исключение
            throw new HttpException(
                "Empty query param in the request: $param"
            );
        }
        return $value;
    }

    // Метод для получения значения
    // определённого заголовка
    /**
     * @throws HttpException
     */
    public function header(string $header): string
    {
        // В суперглобальном массиве $_SERVER
        // имена заголовков имеют префикс 'HTTP_',
        // а знаки подчёркивания заменены на минусы
        $headerName = mb_strtoupper("http_" . str_replace('-', '_', $header));
        if (!array_key_exists($headerName, $this->server)) {
// Если нет такого заголовка - бросаем исключение
            throw new HttpException("No such header in the request: $header");
        }
        $value = trim($this->server[$headerName]);
        if (empty($value)) {
// Если значение заголовка пусто - бросаем исключение
            throw new HttpException("Empty header in the request: $header");
        }
        return $value;
    }
}