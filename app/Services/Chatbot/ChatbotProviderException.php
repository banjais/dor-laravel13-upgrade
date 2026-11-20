<?php

namespace App\Services\Chatbot;

use RuntimeException;

class ChatbotProviderException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $httpStatus = null,
        public readonly ?string $errorCode = null,
        public readonly ?int $retryAfterSeconds = null
    ) {
        parent::__construct($message);
    }
}
