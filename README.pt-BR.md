[![Build Status](https://travis-ci.org/diogocavilha/array-validation.svg?branch=master)](https://travis-ci.org/diogocavilha/array-validation)
[![Latest Stable Version](https://img.shields.io/packagist/v/array/validation.svg?style=flat-square)](https://packagist.org/packages/array/validation)

# Array validation

É uma simples validador de array que utiliza os filtros e validadores nativos do PHP.

# Instalação

```bash
composer require array/validation
```

# Utilização

Método:

- `setFields(array $fieldsRules)`
- `setRequiredFields(array $requiredFieldsRules)`
- `validate(array $input)`
- `isValid(array $input)`
- `removeOnly(array $fieldsToRemove)`
- `getValidArray()`
- `getMessages()`

Validando campos obrigatórios:

> Lança uma `RuntimeException` caso algum campo obrigatório não exista no input.

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

Validando campos opcionais:

> Lança uma `InvalidArgumentException` caso algum campo não tenha um valor válido.

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

Validando ambos:

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

O método `removeOnly` pode ser utilizado para remover alguns campos do input. 

Caso esse método não seja chamado, todos os outros campos que não estão nas regras de validação/filtro serão removidos do input. 

Se você deseja remover apenas alguns campos do niput, o método `removeOnly` pode ser chamado passando um array com os campos que deseja remover.

> Obs: Não é possível remover um campo que exista nas regras de filtro/validação, isso resultará em uma `RuntimeException`.

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

$data = $validatorArray->getValidArray(); // Irá retornar 'id', 'name' e 'age'

$validatorArray->removeOnly(['phone']);
$data = $validatorArray->getValidArray(); // Irá retornar 'id', 'name', 'age' e 'email'
```

Se você não quer que o validador lance excessões automáticas quando a validação não passa, é possível chamar o método `isValid` no lugar de `validade`.

Caso o método `isValid` retorne `false`, o método `getMessages` retornará um array com as mensagens de validação.
