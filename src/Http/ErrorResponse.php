<?php

namespace GeekBrains\LevelTwo\Http;

class ErrorResponse extends Response
{
    protected const SUCCESS = false;
    // Неуспешный ответ содержит строку с причиной неуспеха,
    // по умолчанию - 'Something goes wrong'
    public function __construct(
        private string $reason = 'Something goes wrong'
    ) {
    }

    protected function payload(): array
    {
        return ['reason' => $this->reason];
    }
}