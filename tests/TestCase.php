<?php

declare(strict_types=1);

/*
 * This file is part of Ark PHP Crypto.
 *
 * (c) Ark Ecosystem <info@ark.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ArkEcosystem\Tests\ArkCrypto;

use ArkEcosystem\ArkCrypto\Enums\TransactionBuilder;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $transactionBuilder;
    public function __construct()
    {
        parent::__construct();
        $this->transactionBuilder = new TransactionBuilder();
    }
}
