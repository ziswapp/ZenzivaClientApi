<?php declare(strict_types=1);

namespace Ziswapp\Zenziva\Client;

/**
 * @author Nuradiyana <me@nooradiana.com>
 */
interface MaskingClientInterface extends ClientInterface
{
    /**
     * @return mixed
     */
    public function balance();
}
