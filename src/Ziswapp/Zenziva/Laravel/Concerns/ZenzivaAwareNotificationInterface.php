<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel\Concerns;

use Ziswapp\Zenziva\Message;

interface ZenzivaAwareNotificationInterface
{
    /**
     * @param mixed $notifiable
     * @return Message
     */
    public function toZenziva($notifiable): Message;
}
