[![Build Status](https://travis-ci.org/diogocavilha/array-validation.svg?branch=master)](https://travis-ci.org/diogocavilha/array-validation)
[![Latest Stable Version](https://img.shields.io/packagist/v/array/validation.svg?style=flat-square)](https://packagist.org/packages/array/validation)

# Array validation

É um simples validador de array que utiliza os filtros e validadores nativos do PHP.

# Instalação

```bash
composer require array/validation
```

# Utilização

Métodos:

- `setFields(array $fieldsRules)`
> Adiciona campos opcionais para filtro/validação.

- `setRequiredFields(array $requiredFieldsRules)`
> Adiciona campos obrigatórios para filtro/validação.

- `validate(array $input)`
> Valida um array de entrada. Lança excessão caso a validação não seja satisfeita.

- `isValid(array $input)`
> Valida um array de entrada. Retorna `true` caso o array de entrada seja válido, caso contrário retorna `false`.

- `removeOnly(array $fieldsToRemove)`
> Remove campos que não estão listados nas regras de filtro/validação.

- `getValidArray()`
> Retorna um array com os dados filtrados/validados.

- `getMessages()`
> Retorna um array com as mensagens de validação. Deve ser chamado após a chamada do método `isValid`

### Validando campos obrigatórios:

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

$validator = new SimpleArray();
$validator
    ->setRequiredFields($rules)
    ->validate($arrayToValidate);

$data = $validator->getValidArray();
```

### Validando campos opcionais:

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

$validator = new SimpleArray();
$validator
    ->setFields($rules)
    ->validate($arrayToValidate);

$data = $validator->getValidArray();
```

### Validando ambos:

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

O método `removeOnly` pode ser utilizado para remover alguns campos do array de entrada.

Caso esse método não seja chamado, todos os outros campos que não estão nas regras de filtro/validação serão removidos do array de entrada. 

Se você deseja remover apenas alguns campos do array de entrada, o método `removeOnly` pode ser chamado passando um array com os campos que deseja remover.

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

$validator = new SimpleArray();
$validator
    ->setFields($fieldsRules)
    ->setRequiredFields($requiredFieldsRules)
    ->validate($arrayToValidate);

$data = $validator->getValidArray(); // Irá retornar apenas 'id', 'name' e 'age'

$validator->removeOnly(['phone']);
$data = $validator->getValidArray(); // Irá retornar 'id', 'name', 'age' e 'email'
```

Se você não quer que o validador lance excessões automáticas quando a validação não é satisfeita, é possível chamar o método `isValid` no lugar de `validade`.

Depois disso, o método `getMessages` pode ser chamado para retornar um array com as mensagens de validação.

#### Exemplo:

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
