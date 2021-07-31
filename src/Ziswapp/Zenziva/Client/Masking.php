<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Client;

use Ziswapp\Zenziva\Response\Credit;
use Ziswapp\Zenziva\Response\Outbox;
use Ziswapp\Zenziva\Exception\CreditLimitException;
use Ziswapp\Zenziva\Exception\ZenzivaRequestException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class Masking extends Client implements MaskingClientInterface
{
    /**
     * @var bool
     */
    private $isOtp = false;

    public function setIsOtp(bool $isOtp): void
    {
        $this->isOtp = $isOtp;
    }

    public function isOtp(): bool
    {
        return $this->isOtp;
    }

    /**
     * @return Outbox
     * @throws ClientExceptionInterface
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

        $defaultBody = [
            'userkey' => $this->credential->getKey(),
            'passkey' => $this->credential->getSecret(),
            'res' => 'json',
        ];

        $json = $this->isOtp() ?
            \array_merge($defaultBody, [
                'type' => 'otp',
                'nohp' => $to,
                'pesan' => $message,
            ]) :
            \array_merge($defaultBody, [
                'nohp' => $to,
                'pesan' => $message,
            ]);

        $url = \sprintf('%s/apps/smsapi.php?%s', $this->credential->getUrl(), http_build_query($json));

        $response = $this->httpClient->request('GET', $url);

        $content = $this->parseResponse($response);

        if (\array_key_exists('status', $content) && $content['status'] !== 1) {
            throw new ZenzivaRequestException($content['text']);
        }

        /** @var Outbox $outbox */
        $outbox = Outbox::buildFromArrayContent(
            \array_merge($content, [
                'isiPesan' => $message,
                'msg-status' => $content['text'],
                'tujuan' => $content['to'],
            ])
        );

        return $outbox;
    }

    /**
     * @return Credit
     * @throws ClientExceptionInterface
     * @throws CreditLimitException
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ZenzivaRequestException
     */
    public function balance()
    {
        $query = http_build_query([
            'userkey' => $this->credential->getKey(),
            'passkey' => $this->credential->getSecret(),
        ]);

        $url = \sprintf('%s/apps/getbalance.php?%s', $this->credential->getUrl(), $query);

        $response = $this->httpClient->request('GET', $url);

        $content = $this->parseResponse($response);

        if (\array_key_exists('response', $content)) {
            throw new ZenzivaRequestException($content['response']);
        }

        /** @var Credit $credit */
        $credit = Credit::buildFromArrayContent($content);

        if ($credit->getBalance() <= 0) {
            throw new CreditLimitException();
        }

        return $credit;
    }
}
