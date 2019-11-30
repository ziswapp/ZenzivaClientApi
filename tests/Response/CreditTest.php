<?php declare(strict_types=1);

namespace Tests\Response;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Ziswapp\Zenziva\Response\Credit;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
final class CreditTest extends TestCase
{
    public function testCanCreateFromFactory()
    {
        $content = ['credit' => 100, 'expired' => '31 Desember 2020'];

        $credit = Credit::buildFromArrayContent($content);

        $this->assertInstanceOf(Credit::class, $credit);
        $this->assertSame(100, $credit->getBalance());
        $this->assertSame('31 December 2020', $credit->getExpired()->format('d F Y'));
    }

    public function testCanCreateFromConstruct()
    {
        $content = ['credit' => 100, 'expired' => '31 Desember 2020'];

        $expired = Carbon::createFromLocaleFormat('d F Y', 'id_ID', $content['expired']);

        $credit = new Credit($content['credit'], $expired);

        $this->assertInstanceOf(Credit::class, $credit);
        $this->assertSame(100, $credit->getBalance());
        $this->assertSame('31 December 2020', $credit->getExpired()->format('d F Y'));
    }
}
