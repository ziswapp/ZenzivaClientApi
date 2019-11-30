<?php declare(strict_types=1);

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

    /**
     * @param string $url
     * @param string $key
     * @param string $secret
     */
    public function __construct(string $url, string $key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
