<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\Address as TestClass;

test('it should validate the address', function () {
    $fixture = $this->getFixture('identity');

    $actual = TestClass::validate($fixture['data']['address']);

    expect($actual)->toBeTrue();
});

test('it should fail to validate the address', function () {
    $actual = TestClass::validate('invalid');

    expect($actual)->toBeFalse();
});
