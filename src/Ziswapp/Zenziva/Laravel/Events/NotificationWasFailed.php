<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Events;

use Ziswapp\Zenziva\Message;

final class NotificationWasFailed
{
    /**
     * @var mixed
     */
    public $notifiable;

    public string $errorMessage;

    public ?Message $message = null;

    /**
     * @param mixed $notifiable
     */
    public function __construct(string $errorMessage, $notifiable, ?Message $message = null)
    {
        $this->errorMessage = $errorMessage;
        $this->message = $message;
        $this->notifiable = $notifiable;
    }
}
