# Ark PHP - Crypto

<p align="center">
    <img src="https://github.com/ArkEcosystem/php-crypto/blob/master/banner.png" />
</p>

> A simple PHP Cryptography Implementation for the Ark Blockchain.

[![Build Status](https://travis-ci.org/ArkEcosystem/php-crypto.svg?branch=develop)](https://travis-ci.org/ArkEcosystem/php-crypto)
[![Latest Version](https://img.shields.io/github/release/ArkEcosystem/php-crypto.svg?style=flat-square)](https://github.com/ArkEcosystem/php-crypto/releases)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## Installation

Require this package, with [Composer](https://getcomposer.org/), in the root directory of your project.

```bash
$ composer require arkecosystem/crypto
```

## Documentation

Have a look at the [official documentation](https://docs.ark.io/v1.0/docs/cryptography-php) for advanced examples and features.

## TO-DO

### AIP11 Serialization
- [ ] Transfer **(Type 0)**
- [ ] Second Signature Registration **(Type 1)**
- [ ] Delegate Registration **(Type 2)**
- [ ] Vote **(Type 3)**
- [ ] Multi Signature Registration **(Type 4)**
- [ ] IPFS **(Type 5)**
- [ ] Timelock Transfer **(Type 6)**
- [ ] Multi Payment **(Type 7)**
- [ ] Delegate Resignation **(Type 8)**

### AIP11 Deserialization
- [ ] Transfer **(Type 0)**
- [ ] Second Signature Registration **(Type 1)**
- [ ] Delegate Registration **(Type 2)**
- [ ] Vote **(Type 3)**
- [ ] Multi Signature Registration **(Type 4)**
- [ ] IPFS **(Type 5)**
- [ ] Timelock Transfer **(Type 6)**
- [ ] Multi Payment **(Type 7)**
- [ ] Delegate Resignation **(Type 8)**

### Transaction Signing
- [ ] Transfer **(Type 0)**
- [ ] Second Signature Registration **(Type 1)**
- [ ] Delegate Registration **(Type 2)**
- [ ] Vote **(Type 3)**
- [ ] Multi Signature Registration **(Type 4)**

### Transaction Verifying
- [ ] Transfer **(Type 0)**
- [ ] Second Signature Registration **(Type 1)**
- [ ] Delegate Registration **(Type 2)**
- [ ] Vote **(Type 3)**
- [ ] Multi Signature Registration **(Type 4)**

## Transaction Entity
- [ ] getId
- [ ] sign
- [ ] secondSign
- [ ] parseSignatures
- [ ] serialize
- [ ] deserialize
- [ ] toBytes
- [ ] toArray
- [ ] toJson

### Message
- [ ] sign
- [ ] verify
- [ ] toArray
- [ ] toJson

### Private Key Identity
- [ ] fromPassphrase
- [ ] fromHex

### Public Key Identity
- [ ] fromPassphrase
- [ ] fromHex

### Address Identity
- [ ] fromPassphrase
- [ ] fromPublicKey
- [ ] fromPrivateKey
- [ ] validate

### WIF Identity
- [ ] fromPassphrase

### Configuration
- [ ] getNetwork
- [ ] setNetwork
- [ ] getFee
- [ ] setFee

### Slot
- [ ] time
- [ ] epoch

## Networks (Mainnet, Devnet & Testnet)
- [ ] epoch
- [ ] version
- [ ] nethash
- [ ] wif

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
