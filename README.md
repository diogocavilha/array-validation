[![Build Status](https://travis-ci.org/diogocavilha/array-validation.svg?branch=master)](https://travis-ci.org/diogocavilha/array-validation)
[![Latest Stable Version](https://img.shields.io/packagist/v/array/validation.svg?style=flat-square)](https://packagist.org/packages/array/validation)

# Array validation

That's a simple array validator which uses the native PHP filters and validators.

# Installing

```bash
composer require array/validation
```

# Usage

Validating required fields:

> It throws a `RuntimeException` in case any required field doesn't exist.

```php
<?php

use Validation\SimpleArray;

$rules = [
    'name' => FILTER_SANITIZE_STRING,
    'age' => FILTER_VALIDATE_INT,
];

$arrayToValidate = [
    'name' => 'Diogo Alexsander',
    'age' => 26,
];

$validatorArray = new SimpleArray();
$validatorArray
    ->setRequiredFields($rules)
    ->validate($arrayToValidate);
```

Validating optional fields:

> It throws an `InvalidArgumentException` in case any field doesn't have a valid value.

```php
<?php

use Validation\SimpleArray;

$rules = [
    'name' => FILTER_SANITIZE_STRING,
    'age' => FILTER_VALIDATE_INT,
];

$arrayToValidate = [
    'name' => 'Diogo Alexsander',
];

$validatorArray = new SimpleArray();
$validatorArray
    ->setFields($rules)
    ->validate($arrayToValidate);
```

Validating both:

```php
<?php

use Validation\SimpleArray;

$fieldsRules = [
    'name' => FILTER_SANITIZE_STRING,
    'age' => FILTER_VALIDATE_INT,
];

$requiredFieldsRules = [
    'id' => FILTER_VALIDATE_INT,
];

$arrayToValidate = [
    'id' => 1,
    'name' => 'Diogo Alexsander',
    'age' => 26,
];

$validatorArray = new SimpleArray();
$validatorArray
    ->setFields($fieldsRules)
    ->setRequiredFields($requiredFieldsRules)
    ->validate($arrayToValidate);
```
