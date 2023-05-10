<?php
namespace GeekBrains\LevelTwo\Blog;

use GeekBrains\LevelTwo\Blog\Exceptions\InvalidArgumentException;
use GeekBrains\LevelTwo\Person\Name;

class User
{
    private UUID $uuid;
    private Name $name;
    private string $username;
    private string $hashedPassword;

    /**
     * @param UUID $uuid
     * @param Name $name
     * @param string $login
     * @param string $password
     */
    public function __construct(UUID $uuid, Name $name, string $login, string $hashedPassword)
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->username = $login;
        $this->hashedPassword = $hashedPassword;
    }

    public function __toString(): string
    {
        return "Юзер $this->uuid с именем $this->name и логином $this->username." . PHP_EOL;
    }

    /**
     * @return UUID
     */
    public function uuid(): UUID
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @param Name $name
     */
    public function setName(Name $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function username(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function hashedPassword(): string
    {
        return $this->hashedPassword;
    }
// Функция для вычисления хеша
    private static function hash(string $password, UUID $uuid): string
    {
        return hash('sha256', $uuid . $password);
    }
// Функция для проверки предъявленного пароля
    public function checkPassword(string $password): bool
    {
        return $this->hashedPassword === self::hash($password, $this->uuid);
    }
// Функция для создания нового пользователя

    /**
     * @throws InvalidArgumentException
     */
    public static function createFrom(
        string $username,
        string $password,
        Name $name
    ): self
    {
        $uuid = UUID::random();
        return new self(
            $uuid,
            $name,
            $username,
            self::hash($password,$uuid),
        );
   }
}