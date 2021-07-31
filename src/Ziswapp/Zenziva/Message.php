<?php

declare(strict_types=1);

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

    public function __construct(string $to, string $text)
    {
        $this->to = $to;
        $this->text = $text;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function toArray(): array
    {
        return [
            'to' => $this->to,
            'text' => $this->text,
        ];
    }
}
