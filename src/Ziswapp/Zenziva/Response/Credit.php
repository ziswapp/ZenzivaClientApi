<?php

declare(strict_types=1);

namespace Ziswapp\Zenziva\Response;

use Carbon\Carbon;
use DateTimeInterface;
use Ziswapp\Zenziva\ResponseFactoryInterface;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class Credit implements ResponseFactoryInterface
{
    /**
     * @var int
     */
    private $balance;

    /**
     * @var DateTimeInterface|null
     */
    private $expired;

    public function __construct(int $balance, ?DateTimeInterface $expired = null)
    {
        $this->balance = $balance;

        $this->expired = $expired;
    }

    /**
     * @return ResponseFactoryInterface|self
     */
    public static function buildFromArrayContent(array $content): ResponseFactoryInterface
    {
        $balance = \array_key_exists('credit', $content) ? (int) $content['credit'] : (int) $content['Credit'];

        if (\array_key_exists('expired', $content)) {
            $expiredString = $content['expired'];

            /** @var DateTimeInterface $expiredDate */
            $expiredDate = Carbon::createFromLocaleFormat('d F Y', 'id_ID', $expiredString, 'Asia/Jakarta');

            return new static($balance, $expiredDate);
        }

        return new static($balance);
    }

    public function getBalance(): int
    {
        return $this->balance;
    }

    public function getExpired(): ?DateTimeInterface
    {
        return $this->expired;
    }
}
