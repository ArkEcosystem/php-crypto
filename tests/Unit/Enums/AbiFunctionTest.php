<?php

declare(strict_types=1);

use ArkEcosystem\Crypto\Enums\AbiFunction;
use ArkEcosystem\Crypto\Transactions\Types\Multipayment;
use ArkEcosystem\Crypto\Transactions\Types\Unvote;
use ArkEcosystem\Crypto\Transactions\Types\UsernameRegistration;
use ArkEcosystem\Crypto\Transactions\Types\UsernameResignation;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorRegistration;
use ArkEcosystem\Crypto\Transactions\Types\ValidatorResignation;
use ArkEcosystem\Crypto\Transactions\Types\Vote;

it('should get transaction class', function ($type, $class) {
    expect(constant(AbiFunction::class . '::' . $type)->transactionClass())->toEqual($class);
})->with([
    'Vote'                  => ['VOTE', Vote::class],
    'Unvote'                => ['UNVOTE', Unvote::class],
    'ValidatorRegistration' => ['VALIDATOR_REGISTRATION', ValidatorRegistration::class],
    'ValidatorResignation'  => ['VALIDATOR_RESIGNATION', ValidatorResignation::class],
    'UsernameRegistration'  => ['USERNAME_REGISTRATION', UsernameRegistration::class],
    'UsernameResignation'   => ['USERNAME_RESIGNATION', UsernameResignation::class],
    'Multipayment'          => ['MULTIPAYMENT', Multipayment::class],
]);
