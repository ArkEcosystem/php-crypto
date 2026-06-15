<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\UnitConverter;

test('it should parse units into wei', function () {
    $weiValue = UnitConverter::parseUnits(1, 'wei');
    expect((string) $weiValue)->toBe('1');
});

test('it should parse units into gwei', function () {
    $gweiValue = UnitConverter::parseUnits(1, 'gwei');
    expect((string) $gweiValue)->toBe('1000000000');
});

test('it should parse units into ark', function () {
    $arkValue = UnitConverter::parseUnits(1, 'ark');
    expect((string) $arkValue)->toBe('1000000000000000000');
});

test('it should parse decimal units into ark', function () {
    $arkValueDecimal = UnitConverter::parseUnits(0.1, 'ark');
    expect((string) $arkValueDecimal)->toBe('100000000000000000');
});

test('it should format units from wei', function ($formattedAmount, $amount) {
    $formattedValue = UnitConverter::formatUnits($formattedAmount, 'wei');

    expect((string) $formattedValue)->toEqual($amount);
})->with([
    ['1', '1'],
    ['10', '10'],
    ['100', '100'],
    ['1000', '1000'],
    ['10000', '10000'],
]);

test('it should format units from gwei', function ($formattedAmount, $amount) {
    $formattedValue = UnitConverter::formatUnits($formattedAmount, 'gwei');

    expect((string) $formattedValue)->toEqual($amount);
})->with([
    ['100000001', '0.100000001'],
    ['100000000', '0.1'],
    ['1000000000', '1'],
    ['10000000000', '10'],
    ['100000000000', '100'],
    ['1000000000000', '1000'],
    ['10000000000000', '10000'],
]);

test('it should format units from ark', function ($formattedAmount, $amount) {
    $formattedValue = UnitConverter::formatUnits($formattedAmount, 'ark');

    expect((string) $formattedValue)->toEqual($amount);
})->with([
    ['100000000000000001', '0.100000000000000001'],
    ['100000000000000000', '0.1'],
    ['1000000000000000000', '1'],
    ['10000000000000000000', '10'],
    ['100000000000000000000', '100'],
    ['1000000000000000000000', '1000'],
    ['10000000000000000000000', '10000'],
]);

test('it should throw exception for unsupported unit in parse', function () {
    expect(fn () => UnitConverter::parseUnits(1, 'unsupported'))
        ->toThrow(InvalidArgumentException::class);
});

test('it should throw exception for unsupported unit in format', function () {
    expect(fn () => UnitConverter::formatUnits('1', 'unsupported'))
        ->toThrow(InvalidArgumentException::class);
});

test('it should parse units into ark with fraction', function () {
    $arkValue = UnitConverter::parseUnits(0.1, 'ark');
    expect((string) $arkValue)->toBe('100000000000000000');
});

test('it should convert wei to ark', function () {
    expect(UnitConverter::weiToArk(1, 'DARK'))->toBe('0.000000000000000001 DARK');
    expect(UnitConverter::weiToArk(1))->toBe('0.000000000000000001');
    expect(UnitConverter::weiToArk('1000000000000000000', 'DARK'))->toBe('1 DARK');
    expect(UnitConverter::weiToArk('1000000000000000000'))->toBe('1');
});

test('it should convert gwei to ark', function () {
    expect(UnitConverter::gweiToArk(1, 'DARK'))->toBe('0.000000001 DARK');
    expect(UnitConverter::gweiToArk(1))->toBe('0.000000001');
    expect(UnitConverter::gweiToArk('1000000000', 'DARK'))->toBe('1 DARK');
    expect(UnitConverter::gweiToArk('1000000000'))->toBe('1');
});
