# TSID – Time-Sorted Unique Identifiers

[![Latest Version on Packagist](https://img.shields.io/github/release/odan/tsid.svg)](https://packagist.org/packages/odan/tsid)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://github.com/odan/tsid/workflows/build/badge.svg)](https://github.com/odan/tsid/actions)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/odan/tsid.svg)](https://scrutinizer-ci.com/g/odan/tsid/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/odan/tsid.svg)](https://scrutinizer-ci.com/g/odan/tsid/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/odan/tsid.svg)](https://packagist.org/packages/odan/tsid/stats)

## Description

A library for generating Time Sortable Identifiers (TSID).

This library is a port of [TSID Creator](https://github.com/f4b6a3/tsid-creator) from Java to PHP.

## Requirements

 * PHP 8.0+

## Installation

```
composer require odan/tsid
```

## Usage

```php
use Odan\Tsid\TsidFactory;

$tsidFactory = new TsidFactory();

$tsid = $tsidFactory->generate();

// 388400145978465528
echo $tsid->toInt();

// 0ARYZVZXW377R
echo $tsid->toString();
```

## Database Usage

### MySQL

* Todo

## Read more

* https://vladmihalcea.com/uuid-database-primary-key/
* https://github.com/f4b6a3/tsid-creator

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
