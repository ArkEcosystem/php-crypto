# Ark PHP - Crypto

<p align="center">
    <img src="https://github.com/ArkEcosystem/php-crypto/blob/master/banner.png" />
</p>

> A simple PHP Cryptography Implementation for the Ark Blockchain.

[![Build Status](https://travis-ci.org/ArkEcosystem/php-crypto.svg?branch=develop)](https://travis-ci.org/ArkEcosystem/php-crypto)
[![Latest Version](https://img.shields.io/github/release/ArkEcosystem/php-crypto.svg?style=flat-square)](https://github.com/ArkEcosystem/php-crypto/releases)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## TO-DO

### AIP11 Serialization
- [x] Transfer
- [x] Second Signature Registration
- [x] Delegate Registration
- [x] Vote
- [x] Multi Signature Registration
- [x] IPFS
- [x] Timelock Transfer
- [x] Multi Payment
- [x] Delegate Resignation

### AIP11 Deserialization
- [x] Transfer
- [x] Second Signature Registration
- [x] Delegate Registration
- [x] Vote
- [x] Multi Signature Registration
- [x] IPFS
- [x] Timelock Transfer
- [x] Multi Payment
- [x] Delegate Resignation

### Transaction Signing
- [x] Transfer
- [x] Second Signature Registration
- [x] Delegate Registration
- [x] Vote
- [x] Multi Signature Registration

### Transaction Verifying
- [x] Transfer
- [x] Second Signature Registration
- [x] Delegate Registration
- [x] Vote
- [x] Multi Signature Registration

### Transaction Entity
- [x] getId
- [x] sign
- [x] secondSign
- [x] parseSignatures
- [x] serialize
- [x] deserialize
- [x] toBytes
- [x] toArray
- [x] toJson

### Message
- [x] sign
- [x] verify
- [x] toArray
- [x] toJson

### Private Key Identity
- [x] fromPassphrase
- [x] fromHex

### Public Key Identity
- [x] fromPassphrase
- [x] fromHex

### Address Identity
- [x] fromPassphrase
- [x] fromPublicKey
- [x] fromPrivateKey
- [x] validate

### WIF Identity
- [x] fromPassphrase

### Configuration
- [x] getNetwork
- [x] setNetwork
- [x] getFee
- [x] setFee

### Slot
- [x] time
- [x] epoch

### Networks (Mainnet, Devnet & Testnet)
- [x] epoch
- [x] version
- [x] nethash
- [x] wif

## Installation

Require this package, with [Composer](https://getcomposer.org/), in the root directory of your project.

```bash
$ composer require arkecosystem/crypto
```

## Documentation

Have a look at the [official documentation](https://docs.ark.io/v1.0/docs/cryptography-php) for advanced examples and features.

## Testing

``` bash
$ phpunit
```

## Security

If you discover a security vulnerability within this package, please send an e-mail to security@ark.io. All security vulnerabilities will be promptly addressed.

## Credits

- [Brian Faust](https://github.com/faustbrian)
- [Christopher Wang](https://github.com/christopherjwang) **Initial Cryptography Implementation**
- [All Contributors](../../../../contributors)

## License

[MIT](LICENSE) © [ArkEcosystem](https://ark.io)
