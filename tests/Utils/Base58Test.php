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

namespace ArkEcosystem\Tests\Crypto\Utils;

use ArkEcosystem\Crypto\Utils\Base58;
use ArkEcosystem\Tests\Crypto\TestCase;

/**
 * This is the base58 test class.
 *
 * @author Brian Faust <brian@ark.io>
 * @coversNothing
 */
class Base58Test extends TestCase
{
    /** @test */
    public function can_encode()
    {
        $value = 'Hello World';

        $this->assertSame('JxF12TrwUP45BMd', Base58::encode($value));
    }

    /** @test */
    public function can_decode()
    {
        $value = 'JxF12TrwUP45BMd';

        $this->assertSame('Hello World', Base58::decode($value));
    }

    /** @test */
    public function can_encode_check()
    {
        $value = 'Hello World';

        $this->assertSame('32UWxgjUJDXeRwy6c6Fxf', Base58::encodeCheck($value));
    }

    /** @test */
    public function can_decode_check()
    {
        $value = '32UWxgjUJDXeRwy6c6Fxf';

        $this->assertSame('Hello World', Base58::decodeCheck($value));
    }
}
