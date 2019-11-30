<?php declare(strict_types=1);

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
     * @var DateTimeInterface
     */
    private $expired;

    /**
     * @param int               $balance
     * @param DateTimeInterface $expired
     */
    public function __construct(int $balance, DateTimeInterface $expired)
    {
        $this->balance = $balance;

        $this->expired = $expired;
    }

    /**
     * @param array $content
     *
     * @return ResponseFactoryInterface|self
     */
    public static function buildFromArrayContent(array $content): ResponseFactoryInterface
    {
        $balance = (int) $content['credit'];

        $expiredString = $content['expired'];

        /** @var DateTimeInterface $expiredDate */
        $expiredDate = Carbon::createFromLocaleFormat('d F Y', 'id_ID', $expiredString, 'Asia/Jakarta');

        return new static($balance, $expiredDate);
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return $this->balance;
    }

    /**
     * @return DateTimeInterface
     */
    public function getExpired(): DateTimeInterface
    {
        return $this->expired;
    }
}
