<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Notification;

use Exception;
use Ziswapp\Zenziva\Message;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Events\Dispatcher;
use Ziswapp\Zenziva\Client\ClientInterface;
use Ziswapp\Zenziva\Exception\NotificationException;
use Ziswapp\Zenziva\Laravel\Events\NotificationWasSend;
use Ziswapp\Zenziva\Laravel\Events\NotificationWasFailed;
use Ziswapp\Zenziva\Laravel\Events\NotificationWasSending;
use Ziswapp\Zenziva\Laravel\Concerns\ZenzivaAwareNotificationInterface;

final class ZenzivaChannel
{
    private Dispatcher $event;

    private ClientInterface $client;

    public function __construct(ClientInterface $client, Dispatcher $event)
    {
        $this->event = $event;
        $this->client = $client;
    }

    /**
     * @psalm-suppress UndefinedMethod
     *
     * @param mixed $notifiable
     * @throws NotificationException
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! $notification instanceof ZenzivaAwareNotificationInterface) {
            $errorMessage = 'Notification must be instanceof of ' . ZenzivaAwareNotificationInterface::class;

            $this->event->dispatch(new NotificationWasFailed($errorMessage));

            throw new NotificationException($errorMessage);
        }

        /** @var Message $message */
        $message = $notification->toZenziva($notifiable);

        try {
            $this->event->dispatch(new NotificationWasSending($message));

            $response = $this->client->send($message->getTo(), $message->getText());

            $this->event->dispatch(new NotificationWasSend($message, $response));
        } catch (Exception $exception) {
            $this->event->dispatch(new NotificationWasFailed($exception->getMessage(), $message));

            throw new NotificationException($exception->getMessage(), (int) $exception->getCode(), $exception);
        }
    }
}
