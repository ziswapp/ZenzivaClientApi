<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Notification;

use Exception;
use RuntimeException;
use Ziswapp\Zenziva\Message;
use Illuminate\Notifications\Notification;
use Ziswapp\Zenziva\Client\ClientInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Ziswapp\Zenziva\Exception\NotificationException;
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
        if (!$notification instanceof ZenzivaAwareNotificationInterface) {
            $errorMessage = 'Notification must be instanceof of ' . ZenzivaAwareNotificationInterface::class;

            $this->event->dispatch('zenziva.notification.failed', ['message' => $errorMessage]);

            throw new NotificationException($errorMessage);
        }

        try {
            /** @var Message $message */
            $message = $notification->toZenziva($notifiable);

            $this->event->dispatch('zenziva.notification.sending', ['data' => $message->toArray()]);

            $response = $this->client->send($message->getTo(), $message->getText());

            $this->event->dispatch('zenziva.notification.send', $response);
        }catch (Exception $exception) {
            $this->event->dispatch('zenziva.notification.failed', ['message' => $exception->getMessage()]);

            throw new NotificationException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
