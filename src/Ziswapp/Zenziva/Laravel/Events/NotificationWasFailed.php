<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Events;

use Ziswapp\Zenziva\Message;

final class NotificationWasFailed
{
    public string $errorMessage;

    public ?Message $message = null;

    public function __construct(string $errorMessage, ?Message $message = null)
    {
        $this->errorMessage = $errorMessage;
        $this->message = $message;
    }
}
