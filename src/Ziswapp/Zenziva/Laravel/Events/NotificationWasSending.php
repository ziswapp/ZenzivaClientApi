<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Events;

use Ziswapp\Zenziva\Message;

final class NotificationWasSending
{
    /**
     * @var mixed
     */
    public $notifiable;

    public Message $message;

    /**
     * @param mixed $notifiable
     */
    public function __construct(Message $message, $notifiable)
    {
        $this->message = $message;
        $this->notifiable = $notifiable;
    }
}
