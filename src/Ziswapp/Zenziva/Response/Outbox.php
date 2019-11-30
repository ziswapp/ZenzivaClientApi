<?php declare(strict_types=1);

namespace Ziswapp\Zenziva\Response;

use Carbon\Carbon;
use DateTimeInterface;
use Ziswapp\Zenziva\ResponseFactoryInterface;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class Outbox implements ResponseFactoryInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $to;

    /**
     * @var DateTimeInterface
     */
    private $date;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $status;

    /**
     * @param int               $id
     * @param string            $to
     * @param DateTimeInterface $date
     * @param string            $message
     * @param string            $status
     */
    public function __construct(int $id, string $to, DateTimeInterface $date, string $message, string $status)
    {
        $this->id = $id;
        $this->to = $to;
        $this->date = $date;
        $this->message = $message;
        $this->status = $status;
    }

    /**
     * @param array $content
     *
     * @return ResponseFactoryInterface|self
     *
     * @psalm-suppress PossiblyFalseArgument
     */
    public static function buildFromArrayContent(array $content): ResponseFactoryInterface
    {
        $id = (int) $content['messageId'];

        /** @var DateTimeInterface $expiredDate */
        $date = Carbon::now('Asia/Jakarta');

        if (\array_key_exists('date', $content)) {
            /** @var DateTimeInterface $expiredDate */
            $date = Carbon::createFromFormat('Y-m-d H:i:s', $content['date'], 'Asia/Jakarta');
        }

        $to = \array_key_exists('noTujuan', $content) ? $content['noTujuan'] : $content['tujuan'];

        $message = \array_key_exists('isiPesan', $content) ? $content['isiPesan'] : $content['msg'];

        $status = $content['msg-status'];

        return new static($id, $to, $date, $message, $status);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
