<?php

namespace App\Jobs\DTO;

readonly class SubmitDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $message,
    ) {
    }
}
