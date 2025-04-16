<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Transactions\Builder\EvmCallBuilder;
use ArkEcosystem\Crypto\Utils\UnitConverter;

it('should sign it with a passphrase', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'evm-sign');

    $builder = EvmCallBuilder::new()
        ->gasPrice(UnitConverter::parseUnits($fixture['data']['gasPrice'], 'wei'))
        ->nonce($fixture['data']['nonce'])
        ->network($fixture['data']['network'])
        ->payload($fixture['data']['data'])
        ->gas(UnitConverter::parseUnits($fixture['data']['gas'], 'wei'))
        ->recipientAddress('0xE536720791A7DaDBeBdBCD8c8546fb0791a11901')
        ->sign($this->passphrase);

    expect($builder->verify())->toBeTrue();
});
