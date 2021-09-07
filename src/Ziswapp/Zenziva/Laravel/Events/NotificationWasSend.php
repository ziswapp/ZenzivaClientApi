<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Events;

use Ziswapp\Zenziva\Message;
use Ziswapp\Zenziva\Response\Outbox;

final class NotificationWasSend
{
    /**
     * @var mixed
     */
    public $notifiable;

    public Message $message;

    /**
     * @var Outbox|array
     */
    public $response;

    /**
     * @param Outbox|array $response
     * @param mixed $notifiable
     */
    public function __construct(Message $message, $response, $notifiable)
    {
        $this->message = $message;
        $this->response = $response;
        $this->notifiable = $notifiable;
    }
}
