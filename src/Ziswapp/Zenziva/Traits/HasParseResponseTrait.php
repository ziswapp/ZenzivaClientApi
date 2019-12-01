<?php declare(strict_types=1);

namespace Ziswapp\Zenziva\Traits;

use Symfony\Contracts\HttpClient\ResponseInterface;
use Ziswapp\Zenziva\Exception\ZenzivaRequestException;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
trait HasParseResponseTrait
{
    /**
     * @param ResponseInterface $response
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    protected function parseResponse(ResponseInterface $response): array
    {
        try {
            return $response->toArray();
        } catch (ClientException $exception) {
            $content = $exception->getResponse()->toArray(false);

            if (\array_key_exists('text', $content)) {
                throw new ZenzivaRequestException($content['text']);
            } elseif (\array_key_exists('response', $content)) {
                throw new ZenzivaRequestException($content['response']);
            }
            throw new ZenzivaRequestException($exception->getMessage());
        }
    }
}
