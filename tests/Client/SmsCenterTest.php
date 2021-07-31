<?php

declare(strict_types=1);

namespace Tests\Client;

use DateTime;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Ziswapp\Zenziva\Credential;
use Ziswapp\Zenziva\Response\Inbox;
use Ziswapp\Zenziva\Response\Credit;
use Ziswapp\Zenziva\Response\Outbox;
use Ziswapp\Zenziva\Client\SmsCenter;
use Symfony\Component\HttpClient\MockHttpClient;
use Ziswapp\Zenziva\Exception\CreditLimitException;
use Ziswapp\Zenziva\Exception\CreditExpiredException;
use Ziswapp\Zenziva\Exception\ZenzivaRequestException;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class SmsCenterTest extends TestCase
{
    public function testThrowCredentialException(): void
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Userkey atau Passkey Salah');

        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-request.json'), [
                'http_code' => 400,
            ]),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        $client->balance();
    }

    public function testCanSendingSms(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-balance.json')),
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-send.json')),
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-status.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        $outbox = $client->send('081234567890', 'Hello John!');

        $this->assertInstanceOf(Outbox::class, $outbox);
        $this->assertEquals(157365, $outbox->getId());
        $this->assertSame('Hello John!', $outbox->getMessage());
        $this->assertSame('081234567890', $outbox->getTo());
        $this->assertSame(Carbon::now('Asia/Jakarta')->toDateTimeString(), $outbox->getDate()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        $this->assertSame('Sent', $outbox->getStatus());
    }

    public function testCanCheckStatusSms(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-status.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        $outbox = $client->status('157365');
        $this->assertInstanceOf(Outbox::class, $outbox);
        $this->assertEquals(157365, $outbox->getId());
        $this->assertSame('Hello John!', $outbox->getMessage());
        $this->assertSame('081234567890', $outbox->getTo());
        $this->assertSame(Carbon::now('Asia/Jakarta')->toDateTimeString(), $outbox->getDate()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        $this->assertSame('Sent', $outbox->getStatus());
    }

    public function testCanRequestBalanceEndPoint(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-balance.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        $credit = $client->balance();

        $this->assertInstanceOf(Credit::class, $credit);
        $this->assertSame(999999, $credit->getBalance());
        $this->assertSame('31 December 9999', $credit->getExpired()->format('d F Y'));
    }

    public function testCanRequestWithZeroBalance(): void
    {
        $this->expectException(CreditLimitException::class);

        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/zero-balance.json'))];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        $client->balance();
    }

    public function testCanRequestWithExpiredBalance(): void
    {
        $this->expectException(CreditExpiredException::class);

        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/expired-balance.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        $client->balance();
    }

    public function testCanGetListSmsInbox(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-inbox.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        /** @var Inbox[] $inbox */
        $inbox = $client->inbox(new DateTime(), new DateTime());

        $this->assertCount(3, $inbox);

        $firstInbox = $inbox[0];
        $this->assertInstanceOf(Inbox::class, $firstInbox);
        $this->assertEquals(424, $firstInbox->getId());
        $this->assertSame('2017-11-03 09:04:59', $firstInbox->getDate()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        $this->assertSame('+628123456789', $firstInbox->getFrom());
        $this->assertSame('Hi John', $firstInbox->getMessage());
    }

    public function testCanGetEmptySmsInbox(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/empty-inbox.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        $inbox = $client->inbox(new DateTime(), new DateTime());

        $this->assertCount(0, $inbox);
    }

    public function testCanGetListSmsOutbox(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-outbox.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        /** @var Outbox[] $outbox */
        $outbox = $client->outbox(new DateTime(), new DateTime());

        $this->assertCount(3, $outbox);

        $firstOutbox = $outbox[0];
        $this->assertInstanceOf(Outbox::class, $firstOutbox);
        $this->assertEquals(424, $firstOutbox->getId());
        $this->assertSame('Hi John', $firstOutbox->getMessage());
        $this->assertSame('+628123456789', $firstOutbox->getTo());
        $this->assertSame('2017-11-03 09:04:59', $firstOutbox->getDate()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        $this->assertSame('Sent', $firstOutbox->getStatus());
    }

    public function testCanGetEmptySmsOutbox(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/empty-outbox.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = new SmsCenter($this->credential(), $http);

        /** @var Outbox[] $outbox */
        $outbox = $client->outbox(new DateTime(), new DateTime());

        $this->assertCount(0, $outbox);
    }

    protected function credential(): Credential
    {
        return new Credential('http://foobar.zenziva.co.id/', 'user', 'secret');
    }
}
