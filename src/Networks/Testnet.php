<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Crypto\Networks;

use BitWasp\Bitcoin\Network\Network as TokenNetwork;
use BitWasp\Bitcoin\Network\NetworkFactory;

/**
 * This is the testnet network class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Testnet extends Network
{
    /**
     * {@inheritdoc}
     */
    public static function getMessagePrefix(): string
    {
        return "TEST message:\n";
    }

    /**
     * {@inheritdoc}
     */
    public static function getNethash(): string
    {
        return 'd9acd04bde4234a81addb8482333b4ac906bed7be5a9970ce8ada428bd083192';
    }

    /**
     * {@inheritdoc}
     */
    public static function getWif(): int
    {
        return 186;
    }

    /**
     * {@inheritdoc}
     */
    public static function getFactory(): TokenNetwork
    {
        return NetworkFactory::create('17', '00', '00', true)
            ->setHDPubByte('70617039')
            ->setHDPrivByte('70615956');
    }
}
