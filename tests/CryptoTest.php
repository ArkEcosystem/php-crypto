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

namespace ArkEcosystem\Tests\ArkCrypto;

use ArkEcosystem\ArkCrypto\Crypto;
use ArkEcosystem\Tests\ArkCrypto\TestCase;
use BitWasp\Bitcoin\Network\NetworkFactory;

/**
 * @coversNothing
 */
class CryptoTest extends TestCase
{
    /** @test */
    public function can_get_address_from_public_key()
    {
        // Arrange...
        $publicKey = '022cca9529ec97a772156c152a00aad155ee6708243e65c9d211a589cb5d43234d';
        $address = 'DARiJqhogp2Lu6bxufUFQQMuMyZbxjCydN';

        // Act...
        $result = (new Crypto())->address($publicKey, 0x1E);

        // Assert...
        $this->assertSame($result, $address);
    }

    /** @test */
    public function can_get_wif_from_secret()
    {
        // Arrange...
        $secret = 'this is a top secret passphrase';
        $wif = 'SGq4xLgZKCGxs7bjmwnBrWcT4C1ADFEermj846KC97FSv1WFD1dA';

        // Act...
        $result = (new Crypto())->wif($secret);

        // Assert...
        $this->assertSame($result, $wif);
    }

    /** @test */
    public function test_address_generation()
    {
        // Arrange...
        $secret = 'this is a top secret passphrase';
        $network = NetworkFactory::create('17', '00', '00');

        // Act...
        $address = Crypto::getAddress(Crypto::getKeys($secret), $network);

        // Assert...
        $this->assertSame($address, 'AGeYmgbg2LgGxRW2vNNJvQ88PknEJsYizC');
    }

    /** @test */
    public function test_dark_net_address_generation()
    {
        // Arrange...
        $secret = 'this is a top secret passphrase';
        $network = NetworkFactory::create('1e', '00', '00');

        // Act...
        $address = Crypto::getAddress(Crypto::getKeys($secret), $network);

        // Assert...
        $this->assertSame($address, 'D61mfSggzbvQgTUe6JhYKH2doHaqJ3Dyib');
    }
}
