<?php declare(strict_types=1);

namespace Tests\Client;

use PHPUnit\Framework\TestCase;
use Ziswapp\Zenziva\ClientFactory;
use Symfony\Component\HttpClient\MockHttpClient;
use Ziswapp\Zenziva\Exception\ZenzivaRequestException;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class RegularTest extends TestCase
{
    public function testCanSendSms()
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-regular.json')),
        ];

        $http = new MockHttpClient($responses);

        $client = ClientFactory::regular($http, 'key', 'secret');

        $response = $client->send('081234567890', 'Test sending sms.');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('to', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('text', $response);

        $this->assertSame('081234567890', $response['to']);
        $this->assertSame(0, $response['status']);
        $this->assertSame('Success', $response['text']);
    }

    public function testCanSendSmsXmlResponse()
    {
        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/success-regular.xml')),
        ];

        $http = new MockHttpClient($responses);

        $client = ClientFactory::regular($http, 'key', 'secret');

        $response = $client->send('081234567890', 'Test sending sms.');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('to', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('text', $response);

        $this->assertSame('081234567890', $response['to']);
        $this->assertEquals(0, $response['status']);
        $this->assertSame('Success', $response['text']);
    }

    public function testCanThrowExceptionWithXmlResponse()
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Userkey / Passkey Salah');

        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-regular.xml'))];
        $client1 = ClientFactory::regular(new MockHttpClient($responses), 'key', 'secret');
        $client1->send('081234567890', 'Test sending sms.');

//        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-regular.xml'), ['http_code' => 400])];
//        $client2 = ClientFactory::regular(new MockHttpClient($responses), 'key', 'secret');
//        $client2->send('081234567890', 'Test sending sms.');
//
//
//        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-regular.json'))];
//        $client3 = ClientFactory::regular(new MockHttpClient($responses), 'key', 'secret');
//        $client3->send('081234567890', 'Test sending sms.');
//
//        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-regular.json'), ['http_code' => 400])];
//        $client4 = ClientFactory::regular(new MockHttpClient($responses), 'key', 'secret');
//        $client4->send('081234567890', 'Test sending sms.');
    }

    public function testCanThrowExceptionWithXmlResponseAndErrorCode()
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Userkey / Passkey Salah');

        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-regular.xml'), ['http_code' => 400])];
        $client2 = ClientFactory::regular(new MockHttpClient($responses), 'key', 'secret');
        $client2->send('081234567890', 'Test sending sms.');
    }

    public function testCanThrowExceptionWithJsonResponse()
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Userkey / Passkey Salah');

        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-regular.json'))];
        $client3 = ClientFactory::regular(new MockHttpClient($responses), 'key', 'secret');
        $client3->send('081234567890', 'Test sending sms.');
    }

    public function testCanThrowExceptionWithJsonResponseAndErrorCode()
    {
        $this->expectException(ZenzivaRequestException::class);
        $this->expectExceptionMessage('Userkey / Passkey Salah');

        $responses = [new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-regular.json'), ['http_code' => 400])];
        $client4 = ClientFactory::regular(new MockHttpClient($responses), 'key', 'secret');
        $client4->send('081234567890', 'Test sending sms.');
    }
}
