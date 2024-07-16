<?php

declare(strict_types=1);

namespace ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Key;

use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Adapter\EcAdapter;
use ArkEcosystem\Crypto\EcAdapter\Impl\PhpEcc\Signature\SchnorrSigner;
use ArkEcosystem\Crypto\EcAdapter\Impl\Secp256k1\Signature\SchnorrSignatureInterface;
use ArkEcosystem\Crypto\EcAdapter\Key\XOnlyPublicKeyInterface;
use BitWasp\Bitcoin\Crypto\EcAdapter\Impl\PhpEcc\Key\PrivateKey as PhpEccPrivateKey;
use BitWasp\Bitcoin\Exceptions\InvalidPrivateKey;
use BitWasp\Buffertools\BufferInterface;

class PrivateKey extends PhpEccPrivateKey
{
    /**
     * @var EcAdapter
     */
    public $ecAdapter;

    /**
     * @param EcAdapter $ecAdapter
     * @param \GMP $int
     * @param bool $compressed
     * @throws InvalidPrivateKey
     */
    public function __construct(EcAdapter $ecAdapter, \GMP $int, bool $compressed = false)
    {
        $this->ecAdapter = $ecAdapter;

        parent::__construct($ecAdapter, $int, $compressed);
    }

    public function signSchnorr(BufferInterface $message32): SchnorrSignatureInterface
    {
        $schnorr = new SchnorrSigner($this->ecAdapter);

        return $schnorr->sign($this, $message32);
    }

    // /**
    //  * Return the public key.
    //  *
    //  * @return XOnlyPublicKeyInterface
    //  */
    // public function getXOnlyPublicKey(): XOnlyPublicKeyInterface
    // {
    //     return $this->getPublicKey()->asXOnlyPublicKey();
    // }
}
