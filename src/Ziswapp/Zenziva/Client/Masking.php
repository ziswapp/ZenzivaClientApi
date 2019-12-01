<?php declare(strict_types=1);

namespace Ziswapp\Zenziva\Client;

use Ziswapp\Zenziva\Credential;
use Ziswapp\Zenziva\Response\Credit;
use Ziswapp\Zenziva\Response\Outbox;
use Ziswapp\Zenziva\Exception\CreditLimitException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
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
    private $isOtp;

    /**
     * @param Credential          $credential
     * @param HttpClientInterface $httpClient
     * @param bool                $isOtp
     */
    public function __construct(Credential $credential, HttpClientInterface $httpClient, bool $isOtp = false)
    {
        parent::__construct($credential, $httpClient);

        $this->isOtp = $isOtp;
    }

    /**
     * @param string $to
     * @param string $message
     *
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

        $url = \sprintf('%s/apps/smsapi.php', $this->credential->getUrl());

        $defaultBody = [
            'userkey' => $this->credential->getKey(),
            'passkey' => $this->credential->getSecret(),
            'res' => 'json',
        ];

        $body = $this->isOtp ?
            \array_merge($defaultBody, ['type' => 'otp', 'nohp' => $to, 'pesan' => $message]) :
            \array_merge($defaultBody, ['nohp' => $to, 'pesan' => $message]);

        $response = $this->httpClient->request('POST', $url, [
            \compact('body'),
        ]);

        $content = $this->parseResponse($response);

        if (\array_key_exists('status', $content) && $content['status'] !== 1) {
            throw new ZenzivaRequestException($content['text']);
        }

        /** @var Outbox $outbox */
        $outbox = Outbox::buildFromArrayContent(
            \array_merge($content, ['isiPesan' => $message, 'msg-status' => $content['text'], 'tujuan' => $content['to']])
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
        $url = \sprintf('%s/apps/getbalance.php', $this->credential->getUrl());

        $defaultBody = ['userkey' => $this->credential->getKey(), 'passkey' => $this->credential->getSecret()];

        $response = $this->httpClient->request('GET', $url, [
            'body' => $defaultBody,
        ]);

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
