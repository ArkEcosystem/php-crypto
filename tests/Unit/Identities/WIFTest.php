<?php

declare(strict_types=1);

namespace ArkEcosystem\Tests\Crypto\Unit\Identities;

use ArkEcosystem\Crypto\Identities\WIF;

it('should get the wif from passphrase', function () {
    $fixture = $this->getFixture('identity');

    $actual = WIF::fromPassphrase($fixture['passphrase']);

    expect($actual)->toBe($fixture['data']['wif']);
});
