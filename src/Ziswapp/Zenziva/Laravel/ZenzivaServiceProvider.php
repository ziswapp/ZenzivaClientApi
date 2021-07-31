<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Laravel;

use Ziswapp\Zenziva\ClientFactory;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Ziswapp\Zenziva\Client\ClientInterface;
use Symfony\Component\HttpClient\HttpClient;

final class ZenzivaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ClientInterface::class, function (Container $app): ClientInterface {
            $config = $app->make(Repository::class);

            $key = (string) $config->get('services.zenziva.key');
            $secret = (string) $config->get('services.zenziva.secret');
            $type = (string) $config->get('services.zenziva.type');

            $httpClient = HttpClient::create();

            switch (\mb_strtolower($type)) {
                case 'array':
                    return ClientFactory::array();
                case 'reguler':
                    return ClientFactory::regular($httpClient, $key, $secret);
                case 'masking':
                    return ClientFactory::masking($httpClient, $key, $secret);
                case 'otp':
                    return ClientFactory::otp($httpClient, $key, $secret);
                case 'smscenter':
                    $url = $config->get('zenziva.url');

                    return ClientFactory::center($httpClient, $url, $key, $secret);
                default:
                    throw new \RuntimeException('Zenziva with type ' . $type . ' not supported.');
            }
        });
    }
}
