# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

## 0.2.1 - 2018-07-20

### Fixed
- Properly handle `asset` if empty in `ArkEcosystem\Crypto\Transactions\Transaction`.
- Properly handle `version` and `network` if not set on `ArkEcosystem\Crypto\Transactions\Transaction`.
- Added missing `ArkEcosystem\Crypto\Networks\Devnet` use to `ArkEcosystem\Crypto\Configuration\Network`

## 0.2.0 - 2018-07-18

Several files and folders have been moved around for guideline compliance - see the [diff](https://github.com/ArkEcosystem/php-crypto/compare/0.1.0...0.2.0) for more details

### Fixed
- Multi Payment Serialisation & Deserialisation

### Added
- Slot helper
- Validate PublicKey
- Get Public Key from Hex
- Get Private Key from Hex
- Get Private Key from WIF
- Transaction to Array
- Transaction to JSON

### Changed
- Upgraded `bitwasp/bitcoin` to `0.0.35`

### Removed
- Dropped `nethash` from networks as it was not used

## 0.1.0 - 2018-07-02
- Initial Release
