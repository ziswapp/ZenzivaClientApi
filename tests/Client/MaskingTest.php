<?php

declare(strict_types=1);

namespace Tests\Client;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Ziswapp\Zenziva\ClientFactory;
use Ziswapp\Zenziva\Response\Credit;
use Ziswapp\Zenziva\Response\Outbox;
use Symfony\Component\HttpClient\MockHttpClient;
use Ziswapp\Zenziva\Exception\CreditLimitException;
use Ziswapp\Zenziva\Exception\ZenzivaRequestException;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class MaskingTest extends TestCase
{
    public function testCanThrowExceptionWithHttpCodeError(): void
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Wrong Userkey or Passkey');

        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-masking-balance.json'), [
                'http_code' => 400,
            ]), ];
        $client = ClientFactory::masking(new MockHttpClient($responses), 'key', 'secret');
        $client->balance();
    }

    public function testCanThrowException(): void
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Wrong Userkey or Passkey');

        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-masking-balance.json'))];
        $client = ClientFactory::masking(new MockHttpClient($responses), 'key', 'secret');
        $client->balance();
    }

    public function testCanThrowExceptionWhenSendingSms(): void
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Userkey / Passkey Salah');

        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-masking-balance.json')),
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-masking.json')),
        ];
        $client = ClientFactory::masking(new MockHttpClient($responses), 'key', 'secret');
        $client->send('081318788271', 'Test Pesan');
    }

    public function testThrowExceptionGetBalanceCredit(): void
    {
        $this->expectException(CreditLimitException::class);

        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/empty-masking-balance.json'))];
        $client = ClientFactory::masking(new MockHttpClient($responses), 'key', 'secret');
        $client->balance();
    }

    public function testCanGetBalanceCredit(): void
    {
        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-masking-balance.json'))];
        $client = ClientFactory::masking(new MockHttpClient($responses), 'key', 'secret');
        $credit = $client->balance();

        $this->assertInstanceOf(Credit::class, $credit);
        $this->assertSame(20957, $credit->getBalance());
        $this->assertNull($credit->getExpired());
    }

    public function testCanSendRegularSmsMasking(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-masking-balance.json')),
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-masking.json')),
        ];

        $client = ClientFactory::masking(new MockHttpClient($responses), 'key', 'secret');
        $outbox = $client->send('081318788271', 'Test Pesan');

        $this->assertInstanceOf(Outbox::class, $outbox);
        $this->assertSame('rc18YqgSQZC3vBj9JoWlDAD0', $outbox->getId());
        $this->assertSame('Test Pesan', $outbox->getMessage());
        $this->assertSame('6281318788271', $outbox->getTo());
        $this->assertSame(Carbon::now('Asia/Jakarta')->toDateTimeString(), $outbox->getDate()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        $this->assertSame('Success', $outbox->getStatus());
    }

    public function testCanSendOtpSmsMasking(): void
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-masking-balance.json')),
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-masking.json')),
        ];

        $client = ClientFactory::otp(new MockHttpClient($responses), 'key', 'secret');
        $outbox = $client->send('081318788271', 'Test Pesan');

        $this->assertInstanceOf(Outbox::class, $outbox);
        $this->assertSame('rc18YqgSQZC3vBj9JoWlDAD0', $outbox->getId());
        $this->assertSame('Test Pesan', $outbox->getMessage());
        $this->assertSame('6281318788271', $outbox->getTo());
        $this->assertSame(Carbon::now('Asia/Jakarta')->toDateTimeString(), $outbox->getDate()->format(Carbon::DEFAULT_TO_STRING_FORMAT));
        $this->assertSame('Success', $outbox->getStatus());
    }
}
