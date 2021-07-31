<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Client;

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
final class Regular extends Client implements ClientInterface
{
    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    public function send(string $to, string $message)
    {
        $url = \sprintf('%s/api/sendsms', $this->credential->getUrl());

        $response = $this->httpClient->request('POST', $url, [
            'body' => [
                'userkey' => $this->credential->getKey(),
                'passkey' => $this->credential->getSecret(),
                'nohp' => $to,
                'pesan' => $message,
            ],
        ]);

        return $this->parseResponse($response);
    }

    /**
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
            if ($this->isXml($response->getContent())) {
                return $this->parseXmlResponse($response);
            }

            $content = $response->toArray();

            if ((int) $content['status'] === 1) {
                return $response->toArray();
            }

            throw new ZenzivaRequestException($content['text']);
        } catch (ClientException $exception) {
            if ($this->isXml($exception->getResponse()->getContent(false))) {
                $this->parseXmlResponse($exception->getResponse());
            }

            $content = $exception->getResponse()->toArray(false);

            throw new ZenzivaRequestException($content['text']);
        }
    }

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    protected function parseXmlResponse(ResponseInterface $response)
    {
        $content = \json_decode(\json_encode((array) \simplexml_load_string($response->getContent(false))));

        if ((int) $content->message->status === 0) {
            return [
                'to' => $content->message->to,
                'status' => $content->message->status,
                'text' => $content->message->text,
            ];
        }

        throw new ZenzivaRequestException($content->message->text);
    }

    protected function isXml(string $content): bool
    {
        return \mb_substr($content, 0, 10) === '<response>';
    }
}
