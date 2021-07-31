<?php

declare(strict_types=1);

namespace Tests;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Ziswapp\Zenziva\ClientFactory;
use Ziswapp\Zenziva\Client\Masking;
use Ziswapp\Zenziva\Client\Regular;
use Ziswapp\Zenziva\Client\SmsCenter;
use Ziswapp\Zenziva\Client\ClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Ziswapp\Zenziva\Client\MaskingClientInterface;
use Ziswapp\Zenziva\Client\SmsCenterClientInterface;
use Ziswapp\Zenziva\Exception\TypeNotSupportedException;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class ClientFactoryTest extends TestCase
{
    public function testCanMakeClient(): void
    {
        $http = HttpClient::create();

        $client = ClientFactory::masking($http, 'user', 'secret');
        $this->assertInstanceOf(MaskingClientInterface::class, $client);
        $this->assertInstanceOf(Masking::class, $client);

        $client = ClientFactory::otp($http, 'user', 'secret');
        $this->assertInstanceOf(MaskingClientInterface::class, $client);
        $this->assertInstanceOf(Masking::class, $client);

        $client = ClientFactory::regular($http, 'user', 'secret');
        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(Regular::class, $client);

        $client = ClientFactory::center($http, 'http://foobar.zenziva.co.id', 'user', 'secret');
        $this->assertInstanceOf(SmsCenterClientInterface::class, $client);
        $this->assertInstanceOf(SmsCenter::class, $client);
    }

    public function testCanMakeClientUsingMakeFunction(): void
    {
        $http = HttpClient::create();

        $client = ClientFactory::make($http, ClientFactory::TYPE_MASKING, 'user', 'secret');
        $this->assertInstanceOf(MaskingClientInterface::class, $client);
        $this->assertInstanceOf(Masking::class, $client);

        $client = ClientFactory::make($http, ClientFactory::TYPE_MASKING_OTP, 'user', 'secret');
        $this->assertInstanceOf(MaskingClientInterface::class, $client);
        $this->assertInstanceOf(Masking::class, $client);

        $client = ClientFactory::make($http, ClientFactory::TYPE_REGULAR, 'user', 'secret');
        $this->assertInstanceOf(ClientInterface::class, $client);
        $this->assertInstanceOf(Regular::class, $client);

        $client = ClientFactory::make($http, ClientFactory::TYPE_SMS_CENTER, 'user', 'secret', 'http://foobar.zenziva.co.id');
        $this->assertInstanceOf(SmsCenterClientInterface::class, $client);
        $this->assertInstanceOf(SmsCenter::class, $client);
    }

    public function testThrowExceptionWhenCreateSmsCenterClientWithUrlNotProvide(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('For sms center client, url must be provide.');

        $http = HttpClient::create();

        $client = ClientFactory::make($http, ClientFactory::TYPE_SMS_CENTER, 'user', 'secret');
        $this->assertInstanceOf(SmsCenterClientInterface::class, $client);
        $this->assertInstanceOf(SmsCenter::class, $client);
    }

    public function testThrowExceptionWhenTypeNotSupported(): void
    {
        $this->expectException(TypeNotSupportedException::class);
        $this->expectExceptionMessage(\sprintf('This client type `%s` is not supported.', '10'));

        $http = HttpClient::create();

        ClientFactory::make($http, 10, 'user', 'secret');
    }
}
