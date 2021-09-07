<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Events;

use Ziswapp\Zenziva\Message;

final class NotificationWasSending
{
    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }
}
