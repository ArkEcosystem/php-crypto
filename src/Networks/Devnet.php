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

use ArkEcosystem\Crypto\Contracts\Network;
use BitWasp\Bitcoin\Network\Network as TokenNetwork;
use BitWasp\Bitcoin\Network\NetworkFactory;

/**
 * This is the devnet network class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Devnet extends AbstractNetwork implements Network
{
    /**
     * {@inheritdoc}
     */
    public static function getEpoch(): string
    {
        return '2017-03-21T13:00:00.000Z';
    }

    /**
     * {@inheritdoc}
     */
    public static function getMessagePrefix(): string
    {
        return "DARK message:\n";
    }

    /**
     * {@inheritdoc}
     */
    public static function getNethash(): string
    {
        return '578e820911f24e039733b45e4882b73e301f813a0d2c31330dafda84534ffa23';
    }

    /**
     * {@inheritdoc}
     */
    public static function getWif(): int
    {
        return 170;
    }

    /**
     * {@inheritdoc}
     */
    public static function getFactory(): TokenNetwork
    {
        return NetworkFactory::create('1e', '00', '00')
            ->setHDPubByte('46090600')
            ->setHDPrivByte('46089520');
    }
}
