<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class Credential
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $url;

    public function __construct(string $url, string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->url = $url;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
