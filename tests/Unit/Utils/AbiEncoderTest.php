<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\AbiEncoder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Utils\AbiEncoder
 */
class AbiEncoderTest extends TestCase
{
    private AbiEncoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new AbiEncoder();
    }

    /** @test */
    public function it_should_encode_vote_function_call()
    {
        $functionName        = 'vote';
        $args                = ['0x512F366D524157BcF734546eB29a6d687B762255'];
        $expectedEncodedData = '0x6dd7d8ea000000000000000000000000512f366d524157bcf734546eb29a6d687b762255';

        $encodedData = $this->encoder->encodeFunctionCall($functionName, $args);

        $this->assertSame($expectedEncodedData, $encodedData);
    }
}
