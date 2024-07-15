<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Adapter;

use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter as BitWaspEcAdapter;
use BitWasp\Bitcoin\Crypto\EcAdapter\Key\PrivateKeyInterface;

class EcAdapter extends BitWaspEcAdapter
{
    /**
     * @param  bool|false  $compressed
     * @return <PrivateKeyInterface></PrivateKeyInterface>
     */
    public function getPrivateKey(\GMP $scalar, bool $compressed = false): PrivateKeyInterface
    {
        return new PrivateKey($this, $scalar, $compressed);
    }
}
