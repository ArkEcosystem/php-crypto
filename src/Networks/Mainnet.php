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
 * This is the mainnet network class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class Mainnet extends AbstractNetwork implements Network
{
    /**
     * {@inheritdoc}
     */
    public static function getMessagePrefix(): string
    {
        return "ARK message:\n";
    }

    /**
     * {@inheritdoc}
     */
    public static function getNethash(): string
    {
        return '6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988';
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
        return NetworkFactory::create('17', '00', '00')
            ->setHDPubByte('46090600')
            ->setHDPrivByte('46089520');
    }
}
