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

Use `bigint(20) unsigned` as datatype for the (primary / secondary) key. 

Example:

```sql
CREATE TABLE `users` (
    `id` bigint(20) unsigned NOT NULL,
    `username` varchar(45) NOT NULL,
     PRIMARY KEY (`id`)
) ENGINE=InnoDB
```

**Note:** When you use `BIGINT(20)` the maximum value is 2^63 - 1 == `9223372036854775807`.
This means there is still enough space to store any TSID.
When you use `BIGINT(20) unsigned` the maximum value is: 2^64-1 = `18446744073709551615`

### SQLite

Use `INTEGER` as datatype for the (primary / secondary) key.

```sql
CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT);
```

**Note:** SQLite uses an 8-byte **signed** integer to store integers.
So the maximum positive integer value is 2^63 - 1 == `9223372036854775807`.
This means there is still enough space to store any TSID.

## Data Type Comparison

```
TSID max:                          18446744073709551615
TSID 2023-01-01T00:00:00.000Z:       397177100698290050
TSID 2038-01-19T03:14:07.000Z:      2389272048961164191
TSID 2999-12-31T23:59:59.999Z:      7015104302283010234
PHP_INT_MAX:                        9223372036854775807
SQLite INTEGER max:                 9223372036854775807
MySQL BIGINT(20) max:               9223372036854775807
MySQL BIGINT(20) unsigned max:     18446744073709551615
```

## Read more

* https://vladmihalcea.com/uuid-database-primary-key/
* https://github.com/f4b6a3/tsid-creator

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
