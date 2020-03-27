<?php declare(strict_types=1);

namespace Ziswapp\Zenziva;

final class Message
{
    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $text;

    /**
     * @param string $to
     * @param string $text
     */
    public function __construct(string $to, string $text)
    {
        $this->to = $to;
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
