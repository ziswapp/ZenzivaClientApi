<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Client;

use Ziswapp\Zenziva\Credential;
use Ziswapp\Zenziva\Traits\HasParseResponseTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
abstract class Client
{
    use HasParseResponseTrait;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var Credential
     */
    protected $credential;

    public function __construct(Credential $credential, HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;

        $this->credential = $credential;
    }
}
