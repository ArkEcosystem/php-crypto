<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Identities\PrivateKey;
use ArkEcosystem\Crypto\Transactions\Types\Transfer;

beforeEach(function () {
    $this->fixture = $this->getTransactionFixture('evm_call', 'transfer');

    $this->subject = new Transfer($this->fixture['data']);
});

it('should sign it with a passphrase', function () {
    $this->subject->data['v'] = null;
    $this->subject->data['r'] = null;
    $this->subject->data['s'] = null;

    expect($this->subject->data['v'])->toBeNull();
    expect($this->subject->data['r'])->toBeNull();
    expect($this->subject->data['s'])->toBeNull();

    $this->subject->sign(PrivateKey::fromPassphrase($this->passphrase));

    expect($this->subject->data['v'])->toBe($this->fixture['data']['v']);
    expect($this->subject->data['r'])->toBe($this->fixture['data']['r']);
    expect($this->subject->data['s'])->toBe($this->fixture['data']['s']);
});

it('should recover sender', function () {
    $this->subject->data['from']            = null;
    $this->subject->data['senderPublicKey'] = null;

    expect($this->subject->data['from'])->toBeNull();
    expect($this->subject->data['senderPublicKey'])->toBeNull();

    $this->subject->recoverSender();

    expect($this->subject->data['senderPublicKey'])->toBe($this->fixture['data']['senderPublicKey']);
    expect($this->subject->data['from'])->toBe($this->fixture['data']['from']);
});

it('should verify', function () {
    expect($this->subject->verify())->toBeTrue();
});

it('should get hash', function () {
    expect($this->subject->hash()->getHex())->toBe($this->fixture['data']['hash']);
});

it('should serialize', function () {
    expect($this->subject->serialize()->getHex())->toBe($this->fixture['serialized']);
});

it('should convert to an array', function () {
    expect($this->subject->toArray())->toBe([
        'gasPrice'        => $this->subject->data['gasPrice'],
        'network'         => $this->subject->data['network'],
        'hash'            => $this->subject->data['hash'],
        'gas'             => $this->subject->data['gas'],
        'nonce'           => $this->subject->data['nonce'],
        'senderPublicKey' => $this->subject->data['senderPublicKey'],
        'to'              => $this->subject->data['to'],
        'value'           => $this->subject->data['value'],
        'data'            => $this->subject->data['data'],
        'r'               => $this->subject->data['r'],
        's'               => $this->subject->data['s'],
        'v'               => $this->subject->data['v'],
    ]);
});

it('should omit value if null when converting to array', function () {
    $this->subject->data['gasPrice'] = null;
    $this->subject->data['hash']     = null;
    $this->subject->data['gas']      = null;

    expect($this->subject->toArray())->toBe([
        'network'         => $this->subject->data['network'],
        'nonce'           => $this->subject->data['nonce'],
        'senderPublicKey' => $this->subject->data['senderPublicKey'],
        'to'              => $this->subject->data['to'],
        'value'           => $this->subject->data['value'],
        'data'            => $this->subject->data['data'],
        'r'               => $this->subject->data['r'],
        's'               => $this->subject->data['s'],
        'v'               => $this->subject->data['v'],
    ]);
});

it('should convert to json', function () {
    expect($this->subject->toJson())->toBe(json_encode([
        'gasPrice'        => $this->subject->data['gasPrice'],
        'network'         => $this->subject->data['network'],
        'hash'            => $this->subject->data['hash'],
        'gas'             => $this->subject->data['gas'],
        'nonce'           => $this->subject->data['nonce'],
        'senderPublicKey' => $this->subject->data['senderPublicKey'],
        'to'              => $this->subject->data['to'],
        'value'           => $this->subject->data['value'],
        'data'            => $this->subject->data['data'],
        'r'               => $this->subject->data['r'],
        's'               => $this->subject->data['s'],
        'v'               => $this->subject->data['v'],
    ]));
});

it('should return empty string for the payload', function () {
    expect($this->subject->getPayload())->toBe('');
});

it('should handle large amounts', function () {
    $fixture = $this->getTransactionFixture('evm_call', 'transfer-large-amount');

    $subject = new Transfer($fixture['data']);

    expect((string) $subject->data['gasPrice'])->toBe((string) $fixture['data']['gasPrice']);
    expect($subject->data['nonce'])->toBe($fixture['data']['nonce']);
    expect($subject->data['network'])->toBe($fixture['data']['network']);
    expect((string) $subject->data['gas'])->toBe((string) $fixture['data']['gas']);
    expect($subject->data['to'])->toBe($fixture['data']['to']);
    expect((string) $subject->data['value'])->toBe((string) $fixture['data']['value']);
    expect($subject->data['v'])->toBe($fixture['data']['v']);
    expect($subject->data['r'])->toBe($fixture['data']['r']);
    expect($subject->data['s'])->toBe($fixture['data']['s']);

    expect($subject->serialize()->getHex())->toBe($fixture['serialized']);

    expect($subject->data['hash'])->toBe($fixture['data']['hash']);

    expect($subject->verify())->toBeTrue();
});
