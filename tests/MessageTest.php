<?php declare(strict_types=1);

namespace Tests;

use Ziswapp\Zenziva\Message;
use PHPUnit\Framework\TestCase;

final class MessageTest extends TestCase
{
    public function testCanMakeClient()
    {
        $to = '081318788271';
        $text = 'Test message';

        $message = new Message($to, $text);

        $this->assertInstanceOf(Message::class, $message);
        $this->assertSame($to, $message->getTo());
        $this->assertSame($text, $message->getText());
    }
}
