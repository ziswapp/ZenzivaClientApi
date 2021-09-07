<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Events;

use Ziswapp\Zenziva\Message;
use Ziswapp\Zenziva\Response\Outbox;

final class NotificationWasSend
{
    public Message $message;

    /**
     * @var Outbox|array
     */
    public $response;

    /**
     * @param Outbox|array $response
     */
    public function __construct(Message $message, $response)
    {
        $this->message = $message;
        $this->response = $response;
    }
}
