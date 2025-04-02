<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Utils\Slot;

test('it should get the time', function () {
    $actual = Slot::time();

    expect($actual)->toBeInt();
});

test('it should get the epoch', function () {
    $actual = Slot::epoch();

    expect($actual)->toBeInt();
});
