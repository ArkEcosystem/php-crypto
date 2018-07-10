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

use ArkEcosystem\Crypto\Configuration\Network as NetworkConfiguration;
use ArkEcosystem\Crypto\Contracts\Network;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as EcPrivateKey;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Bitcoin\Key\PrivateKeyFactory;
use BitWasp\Buffertools\Buffer;

/**
 * This is the private key class.
 *
 * @author Brian Faust <brian@ark.io>
 */
class PrivateKey
{
    /**
     * Derive the private key for the given secret.
     *
     * @param string $secret
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey
     */
    public static function fromSecret(string $secret): EcPrivateKey
    {
        $secret = Hash::sha256(new Buffer($secret));

        return PrivateKeyFactory::fromHex($secret, true);
    }

    /**
     * Derive the private key for the given WIF.
     *
     * @param string $wif
     * @param \ArkEcosystem\Crypto\Contracts\Network|null $network
     *
     * @return \BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey
     */
    public static function fromWif(string $wif, Network $network = null): EcPrivateKey
    {
        $network = $network ?? NetworkConfiguration::get();

        return PrivateKeyFactory::fromWif($wif, null, $network->getFactory());
    }
}
