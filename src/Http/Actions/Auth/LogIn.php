<?php

namespace GeekBrains\LevelTwo\Http\Actions\Auth;

use DateTimeImmutable;
use Exception;
use GeekBrains\LevelTwo\Blog\AuthToken;
use GeekBrains\LevelTwo\Blog\Repositories\AuthTokensRepository\AuthTokensRepositoryInterface;
use GeekBrains\LevelTwo\Http\Auth\AuthException;
use GeekBrains\LevelTwo\Http\Auth\PasswordAuthenticationInterface;
use GeekBrains\LevelTwo\Http\ErrorResponse;
use GeekBrains\LevelTwo\Http\Request;
use GeekBrains\LevelTwo\Http\Response;
use GeekBrains\LevelTwo\Http\SuccessfulResponse;

class LogIn
{
    public function __construct(
    // Авторизация по паролю
        private PasswordAuthenticationInterface $passwordAuthentication,
    // Репозиторий токенов
        private AuthTokensRepositoryInterface $authTokensRepository
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
    // Аутентифицируем пользователя
        try {
            $user = $this->passwordAuthentication->user($request);
        } catch (AuthException $e) {
            return new ErrorResponse($e->getMessage());
        }
    // Генерируем токен
        $authToken = new AuthToken(
    // Случайная строка длиной 40 символов
    bin2hex(random_bytes(40)),
    $user->uuid(),
    // Срок годности - 1 день
    (new DateTimeImmutable())->modify('+1 day')
    );
    // Сохраняем токен в репозиторий
    $this->authTokensRepository->save($authToken);
    // Возвращаем токен
    return new SuccessfulResponse([
        'token' => $authToken->token(),
    ]);
    }
}