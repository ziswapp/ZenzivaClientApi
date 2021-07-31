<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Client;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
interface ClientInterface
{
    /**
     * @return mixed
     */
    public function send(string $to, string $message);
}
