<?php

declare(strict_types=1);

namespace Tests\Traits;

use PHPUnit\Framework\TestCase;
use Ziswapp\Zenziva\ClientFactory;
use Symfony\Component\HttpClient\MockHttpClient;
use Ziswapp\Zenziva\Exception\ZenzivaRequestException;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class HasParseResponseTraitTest extends TestCase
{
    public function testTraitCanThrowExceptionWithHttpCodeError(): void
    {
        $this->expectException(ZenzivaRequestException::class);

        $responses = [
            new MockResponse(\file_get_contents(__DIR__ . '/stubs/error-masking.json'), [
                'http_code' => 400,
            ]), ];
        $client = ClientFactory::masking(new MockHttpClient($responses), 'key', 'secret');
        $client->balance();
    }
}
