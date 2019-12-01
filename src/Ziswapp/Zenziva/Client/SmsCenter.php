<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Client;

use Carbon\Carbon;
use DateTimeInterface;
use Ziswapp\Zenziva\Response\Inbox;
use Ziswapp\Zenziva\Response\Credit;
use Ziswapp\Zenziva\Response\Outbox;
use Ziswapp\Zenziva\Exception\CreditLimitException;
use Ziswapp\Zenziva\Exception\CreditExpiredException;
use Ziswapp\Zenziva\Exception\ZenzivaRequestException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

final class SmsCenter extends Client implements SmsCenterClientInterface
{
    /**
     * @param string $to
     * @param string $message
     *
     * @return Outbox
     * @throws ClientExceptionInterface
     * @throws CreditExpiredException
     * @throws CreditLimitException
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    public function send(string $to, string $message)
    {
        $this->balance();

        $url = \sprintf('%s/api/sendsms', $this->credential->getUrl());

        $response = $this->httpClient->request('POST', $url, [
            'body' => [
                'userkey' => $this->credential->getKey(),
                'passkey' => $this->credential->getSecret(),
                'nohp' => $to,
                'pesan' => $message,
            ],
        ]);

        $messageId = (string) $this->parseResponse($response)['messageId'];

        return $this->status($messageId);
    }

    /**
     * @param string $messageId
     *
     * @return Outbox
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    public function status(string $messageId)
    {
        $url = $this->buildUrl('report');

        $response = $this->httpClient->request('GET', $url, [
            'query' => \compact('messageId'),
        ]);

        $content = $this->parseResponse($response);

        /** @var Outbox $outbox */
        $outbox = Outbox::buildFromArrayContent($content);

        return $outbox;
    }

    /**
     * @return Credit
     * @throws ClientExceptionInterface
     * @throws CreditExpiredException
     * @throws CreditLimitException
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    public function balance()
    {
        $url = $this->buildUrl('balance');

        $response = $this->httpClient->request('GET', $url);

        /** @var Credit $credit */
        $credit = Credit::buildFromArrayContent(
            $this->parseResponse($response)
        );

        if ($credit->getBalance() <= 0) {
            throw new CreditLimitException();
        }

        if ($credit->getExpired() !== null && $credit->getExpired() < Carbon::now('Asia/Jakarta')) {
            throw new CreditExpiredException();
        }

        return $credit;
    }

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    public function inbox(DateTimeInterface $startDate, DateTimeInterface $endDate)
    {
        $url = $this->buildUrl('getinbox');

        $response = $this->httpClient->request('GET', $url, [
            'query' => [
                'start_date' => $startDate->format('d/m/Y'),
                'end_date' => $endDate->format('d/m/Y'),
            ],
        ]);

        $content = $this->parseResponse($response);

        if (! \array_key_exists('msg-count', $content)) {
            return [];
        }

        return \array_map(function (array $message) {
            return Inbox::buildFromArrayContent($message);
        }, $content['msg']);
    }

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    public function outbox(DateTimeInterface $startDate, DateTimeInterface $endDate)
    {
        $url = $this->buildUrl('getoutbox');

        $response = $this->httpClient->request('GET', $url, [
            'query' => [
                'start_date' => $startDate->format('d/m/Y'),
                'end_date' => $endDate->format('d/m/Y'),
            ],
        ]);

        $content = $this->parseResponse($response);

        if (! \array_key_exists('msg-count', $content)) {
            return [];
        }

        return \array_map(function (array $message) {
            return Outbox::buildFromArrayContent($message);
        }, $content['msg']);
    }

    /**
     * @param string $endPoint
     *
     * @return string
     */
    private function buildUrl(string $endPoint): string
    {
        return \sprintf(
            '%s/api/%s/?userkey=%s&passkey=%s',
            $this->credential->getUrl(),
            $endPoint,
            $this->credential->getKey(),
            $this->credential->getSecret()
        );
    }
}
