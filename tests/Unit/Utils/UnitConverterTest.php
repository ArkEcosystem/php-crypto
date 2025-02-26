<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Utils;

use ArkEcosystem\Crypto\Utils\UnitConverter;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \ArkEcosystem\Crypto\Utils\UnitConverter
 */
class UnitConverterTest extends TestCase
{
    /** @test */
    public function it_should_parse_units_into_wei()
    {
        $weiValue = UnitConverter::parseUnits(1, 'wei');
        $this->assertSame('1', (string) $weiValue);
    }

    /** @test */
    public function it_should_parse_units_into_gwei()
    {
        $gweiValue = UnitConverter::parseUnits(1, 'gwei');
        $this->assertSame('1000000000', (string) $gweiValue);
    }

    /** @test */
    public function it_should_parse_units_into_ark()
    {
        $arkValue = UnitConverter::parseUnits(1, 'ark');
        $this->assertSame('1000000000000000000', (string) $arkValue);
    }

    /** @test */
    public function it_should_parse_decimal_units_into_ark()
    {
        $arkValueDecimal = UnitConverter::parseUnits(0.1, 'ark');
        $this->assertSame('100000000000000000', (string) $arkValueDecimal);
    }

    /** @test */
    public function it_should_format_units_from_wei()
    {
        $formattedValue = UnitConverter::formatUnits('1', 'wei');
        $this->assertSame(1.0, $formattedValue);
    }

    /** @test */
    public function it_should_format_units_from_gwei()
    {
        $formattedValue = UnitConverter::formatUnits('1000000000', 'gwei');
        $this->assertSame(1.0, $formattedValue);
    }

    /** @test */
    public function it_should_format_units_from_ark()
    {
        $formattedValue = UnitConverter::formatUnits('1000000000000000000', 'ark');
        $this->assertSame(1.0, $formattedValue);
    }

    /** @test */
    public function it_should_throw_exception_for_unsupported_unit_in_parse()
    {
        $this->expectException(InvalidArgumentException::class);
        UnitConverter::parseUnits(1, 'unsupported');
    }

    /** @test */
    public function it_should_throw_exception_for_unsupported_unit_in_format()
    {
        $this->expectException(InvalidArgumentException::class);
        UnitConverter::formatUnits('1', 'unsupported');
    }

    /** @test */
    public function it_should_parse_units_into_ark_with_fraction()
    {
        $arkValue = UnitConverter::parseUnits(0.1, 'ark');
        $this->assertSame('100000000000000000', (string) $arkValue);
    }

    /** @test */
    public function it_should_convert_wei_to_ark()
    {
        $this->assertSame('0.000000000000000001 DARK', UnitConverter::weiToArk(1, 'DARK'));
        $this->assertSame('0.000000000000000001', UnitConverter::weiToArk(1));
        $this->assertSame('1 DARK', UnitConverter::weiToArk('1000000000000000000', 'DARK'));
        $this->assertSame('1', UnitConverter::weiToArk('1000000000000000000'));
    }

    /** @test */
    public function it_should_convert_gwei_to_ark()
    {
        $this->assertSame('0.000000001 DARK', UnitConverter::gweiToArk(1, 'DARK'));
        $this->assertSame('0.000000001', UnitConverter::gweiToArk(1));
        $this->assertSame('1 DARK', UnitConverter::gweiToArk('1000000000', 'DARK'));
        $this->assertSame('1', UnitConverter::gweiToArk('1000000000'));
    }
}
