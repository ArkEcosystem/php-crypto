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

namespace ArkEcosystem\Crypto\Identity;

use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;
use BrianFaust\Binary\UnsignedInteger\Writer;

/**
 * This is the wif class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class WIF
{
    /**
     * Derive the WIF from the given secret.
     *
     * @param string $secret
     * @param int    $wif
     *
     * @return string
     */
    public static function fromSecret(string $secret, int $wif = 0xaa): string
    {
        $secret = Hash::sha256(new Buffer($secret));
        $seed   = Writer::bit8($wif);
        $seed .= $secret->getBinary();
        $seed .= Writer::bit8(0x01);

        return Base58::encodeCheck(new Buffer($seed));
    }
}
