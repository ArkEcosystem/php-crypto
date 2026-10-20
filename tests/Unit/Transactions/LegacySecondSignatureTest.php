<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Configuration\Network;
use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Networks\Testnet;
use ArkEcosystem\Crypto\Transactions\Deserializer;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;

// Test vector from the Mainsail LSS v1 spec, built for chain 10_000
beforeEach(function () {
    $this->fixture = $this->getTransactionFixture('evm_call', 'transfer-legacy-second-signature-vector');

    Network::set(new class($this->fixture['chainId']) extends Testnet {
        public function __construct(private readonly int $id)
        {
            parent::__construct();
        }

        public function chainId(): int
        {
            return $this->id;
        }
    });

    $this->unsignedData = array_diff_key($this->fixture['data'], array_flip(['legacySecondSignature', 'hash']));
});

it('should sign the hash of the signed transaction with the second key', function () {
    $transaction = new Transfer($this->unsignedData);

    $transaction->sign(PrivateKey::fromHex($this->fixture['privateKey']));

    expect($transaction->serialize()->getHex())->toBe($this->fixture['signed']);
    expect($transaction->hash()->getHex())->toBe($this->fixture['data']['hash']);

    $transaction->legacySecondSign(PrivateKey::fromHex($this->fixture['secondPrivateKey']));

    expect($transaction->data['legacySecondSignature'])->toBe($this->fixture['data']['legacySecondSignature']);
    expect($transaction->serialize()->getHex())->toBe($this->fixture['serialized']);
    expect($transaction->hash()->getHex())->toBe($this->fixture['data']['hash']);
});

it('should use a 0 or 1 recovery id', function () {
    $transaction = new Transfer($this->unsignedData);

    $transaction->sign(PrivateKey::fromHex($this->fixture['privateKey']));
    $transaction->legacySecondSign(PrivateKey::fromHex($this->fixture['secondPrivateKey']));

    expect(substr($transaction->data['legacySecondSignature'], -2))->toBeIn(['00', '01']);
});

it('should require the primary signature first', function () {
    $transaction = new Transfer($this->unsignedData);

    $transaction->legacySecondSign(PrivateKey::fromHex($this->fixture['secondPrivateKey']));
})->throws(RuntimeException::class, 'Transaction must be signed before adding a legacy second signature');

it('should deserialize and hash only the first 9 fields', function () {
    $transaction = Deserializer::new($this->fixture['serialized'])->deserialize();

    expect($transaction->data['legacySecondSignature'])->toBe($this->fixture['data']['legacySecondSignature']);
    expect($transaction->data['hash'])->toBe($this->fixture['data']['hash']);
    expect($transaction->serialize()->getHex())->toBe($this->fixture['serialized']);
    expect($transaction->verify())->toBeTrue();
});
