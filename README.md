[![Build Status](https://travis-ci.org/diogocavilha/array-validation.svg?branch=master)](https://travis-ci.org/diogocavilha/array-validation)
[![Latest Stable Version](https://img.shields.io/packagist/v/array/validation.svg?style=flat-square)](https://packagist.org/packages/array/validation)

# Array validation

That's a simple array validator which uses the native PHP filters and validators.

# Installing

```bash
composer require array/validation
```

# Usage

Methods:

- `setFields(array $fieldsRules)`
- `setRequiredFields(array $requiredFieldsRules)`
- `validate(array $input)`
- `isValid(array $input)`
- `removeOnly(array $fieldsToRemove)`
- `getValidArray()`
- `getMessages()`

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

$data = $validatorArray->getValidArray();
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

$data = $validatorArray->getValidArray();
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

$data = $validatorArray->getValidArray();
```

The method `removeOnly` can be used for removing some fields from input array. In case you don't call this method, all the other fields will be removed from input array and you will get only the fields you want to validate. If you don't want to lose all the other fields, but even so you want to remove some of them, so you can call `removeOnly` method by passing an array containing the fields you want to remove.

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
    'email' => 'unwanted',
    'phone' => 'unwanted',
];

$validatorArray = new SimpleArray();
$validatorArray
    ->setFields($fieldsRules)
    ->setRequiredFields($requiredFieldsRules)
    ->validate($arrayToValidate);

$data = $validatorArray->getValidArray(); // It will return only 'id', 'name' and 'age'

$validatorArray->removeOnly(['phone']);
$data = $validatorArray->getValidArray(); // It will return 'id', 'name', 'age' and 'email'
```
