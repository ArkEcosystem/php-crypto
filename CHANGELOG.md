# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased

## 1.0.0 - 2020-02-11

### Added

- Implement AIP11

## 0.4.0 - 2018-12-19

### Changed

- Support vendor fields up to 255 characters

## 0.3.0 - 2018-12-19

### Changed

- Upgraded `bitwasp/bitcoin` to `1.0.0`

## 0.2.5 - 2018-12-07

### Fixed

- Handle signed messages that were created through Desktop Wallet 2.0

## 0.2.4 - 2018-11-08

### Fixed

- Missing ID from delegate registrations
- Maximum vendor field length _(previously 63, now 64)_

## 0.2.3 - 2018-09-30

### Fixed

- Skip recipient id in `toBytes` for type 1 and 4 transactions.

## 0.2.2 - 2018-07-31

### Fixed

- Properly calculate the transaction `id` if signed with second signature.

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
