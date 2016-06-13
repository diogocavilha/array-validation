[![Build Status](https://travis-ci.org/diogocavilha/array-validation.svg?branch=master)](https://travis-ci.org/diogocavilha/array-validation)
[![Latest Stable Version](https://img.shields.io/packagist/v/array/validation.svg?style=flat-square)](https://packagist.org/packages/array/validation)

[Documentação em português/Portuguese documentation](https://github.com/diogocavilha/array-validation/blob/dev-master/README.pt-BR.md)

# Array validation

It's a simple array validator which uses native filters and validators from PHP.

# Installing

```bash
composer require array/validation
```

# Usage

Methods:

- `setFields(array $fieldsRules)`
> It adds optional fields to filter/validate.

- `setRequiredFields(array $requiredFieldsRules)`
> It adds required fields to filter/validate.

- `validate(array $input)`
> It validates an input array. It throws an exception in case the validation is not satisfied.

- `isValid(array $input)`
> It validates an input array. It returns `true` in case the input array is valid, otherwise it returns `false`.

- `removeOnly(array $fieldsToRemove)`
> It removes fields that are not in filter/validation rules.

- `getValidArray()`
> It returns an array containing the filtered/validated data.

- `getMessages()`
> It returns an array containing the validation messages. This method should be called after calling the `isValid`.

### Validating required fields:

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

$validator = new SimpleArray();
$validator
    ->setRequiredFields($rules)
    ->validate($arrayToValidate);

$data = $validator->getValidArray();
```

### Validating optional fields:

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

$validator = new SimpleArray();
$validator
    ->setFields($rules)
    ->validate($arrayToValidate);

$data = $validator->getValidArray();
```

### Validating both:

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

$validator = new SimpleArray();
$validator
    ->setFields($fieldsRules)
    ->setRequiredFields($requiredFieldsRules)
    ->validate($arrayToValidate);

$data = $validator->getValidArray();
```

The `removeOnly` method can be used to remove a few fields from input array.

In case it's not called, all the other fields that are not present in the filter/validation rules will be removed from input array.

If you wish to remove just a few fields from input array, the method `removeOnly` can be called by passing an array containing the fields you wish to remove.

> Ps: It's not possible to remove a field that is present in filter/validation rules, if so, it will throw a `RuntimeException`.

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

$validator = new SimpleArray();
$validator
    ->setFields($fieldsRules)
    ->setRequiredFields($requiredFieldsRules)
    ->validate($arrayToValidate);

$data = $validator->getValidArray(); // It will return only 'id', 'name' and 'age'

$validator->removeOnly(['phone']);
$data = $validator->getValidArray(); // It will return 'id', 'name', 'age' and 'email'
```

If you don't want validator automatically throw exceptions when the validation is not satisfied, it's possible to call the method `isValid` instead of `validate`.

After that, you can call the method `getMessages` to get an array containing the validation messages.

#### Sample:

```php
<?php

$input = [
    'name' => '<strong>Diogo</strong>',
    'description' => "<b>This is a test</b>, to know more about it <a href='index.phtml'>click here</a>",
    'email' => 'email@domain.com',
    'phone' => '5555555 - test',
];

$rules = [
    'phone' => FILTER_VALIDATE_INT,
    'name' => FILTER_SANITIZE_STRING,
    'description' => FILTER_SANITIZE_STRING
];

$rulesRequired = [
    'id' => FILTER_VALIDATE_INT,
    'code' => FILTER_VALIDATE_INT,
];

$validator = new SimpleArray();
$validator
    ->setFields($rules)
    ->setRequiredFields($rulesRequired);
    
if (!$validator->isValid($input)) {
    $messages = $validator->getMessages();
    foreach ($messages as $message) {
        echo $message, '<br>';
    }
}
```
