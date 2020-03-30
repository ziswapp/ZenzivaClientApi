<?php declare(strict_types=1);

namespace Ziswapp\Zenziva;

use RuntimeException;
use Ziswapp\Zenziva\Client\Masking;
use Ziswapp\Zenziva\Client\Regular;
use Ziswapp\Zenziva\Client\SmsCenter;
use Ziswapp\Zenziva\Client\ClientInterface;
use Ziswapp\Zenziva\Client\MaskingClientInterface;
use Ziswapp\Zenziva\Client\SmsCenterClientInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Ziswapp\Zenziva\Exception\TypeNotSupportedException;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class ClientFactory
{
    public const TYPE_MASKING = 0;

    public const TYPE_MASKING_OTP = 1;

    public const TYPE_REGULAR = 2;

    public const TYPE_SMS_CENTER = 3;

    /**
     * @param HttpClientInterface $httpClient
     * @param int                 $type
     * @param string              $key
     * @param string              $secret
     * @param string|null         $url
     *
     * @return ClientInterface|MaskingClientInterface|SmsCenterClientInterface
     */
    public static function make(HttpClientInterface $httpClient, int $type, string $key, string $secret, ?string $url = null)
    {
        switch ($type) {
            case self::TYPE_MASKING:
                return self::masking($httpClient, $key, $secret);
            case self::TYPE_MASKING_OTP:
                return self::otp($httpClient, $key, $secret);
            case self::TYPE_REGULAR:
                return self::regular($httpClient, $key, $secret);
            case self::TYPE_SMS_CENTER:
                if ($url === null) {
                    throw new RuntimeException('For sms center client, url must be provide.');
                }

                return self::center($httpClient, $url, $key, $secret);
            default:
                throw new TypeNotSupportedException(\sprintf('This client type `%i` is not supported.', $type));
        }
    }

    /**
     * @param HttpClientInterface $httpClient
     * @param string              $key
     * @param string              $secret
     *
     * @return MaskingClientInterface
     */
    public static function masking(HttpClientInterface $httpClient, string $key, string $secret): MaskingClientInterface
    {
        return new Masking(new Credential('https://alpha.zenziva.net', $key, $secret), $httpClient);
    }

    /**
     * @param HttpClientInterface $httpClient
     * @param string              $key
     * @param string              $secret
     *
     * @return MaskingClientInterface
     */
    public static function otp(HttpClientInterface $httpClient, string $key, string $secret): MaskingClientInterface
    {
        /** @var Masking $client */
        $client = static::masking($httpClient, $key, $secret);

        $client->setIsOtp(true);

        return $client;
    }

    /**
     * @param HttpClientInterface $httpClient
     * @param string              $key
     * @param string              $secret
     *
     * @return ClientInterface
     */
    public static function regular(HttpClientInterface $httpClient, string $key, string $secret): ClientInterface
    {
        return new Regular(new Credential('https://gsm.zenziva.net', $key, $secret), $httpClient);
    }

    /**
     * @param HttpClientInterface $httpClient
     * @param string              $url
     * @param string              $key
     * @param string              $secret
     *
     * @return SmsCenterClientInterface
     */
    public static function center(HttpClientInterface $httpClient, string $url, string $key, string $secret): SmsCenterClientInterface
    {
        return new SmsCenter(new Credential($url, $key, $secret), $httpClient);
    }
}
