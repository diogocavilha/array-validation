<?php

namespace Validation;

use \InvalidArgumentException;
use \RuntimeException;

/**
 * Validates an array of fields => rules against a input array of fields => values.
 * Rules are the default filters and validators from php.
 *
 * @author Diogo Alexsander Cavilha <diogocavilha@gmail.com>
 */
class SimpleArray
{
    private $requiredFields = [];
    private $fields = [];

    /**
     * Sets an array of required fields to be checked.
     *
     * @param array $requiredFields Required fields/rules to be verified against the original array.
     * @throws InvalidArgumentException
     * @access public
     */
    public function setRequiredFields(array $requiredFields)
    {
        if (empty($requiredFields)) {
            throw new InvalidArgumentException(sprintf('%s: A non empty array is expected.', __METHOD__));
        }

        $this->requiredFields = $requiredFields;
        return $this;
    }

    /**
     * Sets an array of optional fields to be checked.
     *
     * @param array $fields Fields/rules to be verified against the original array.
     * @throws InvalidArgumentException
     * @access public
     */
    public function setFields(array $fields)
    {
        if (empty($fields)) {
            throw new InvalidArgumentException(sprintf('%s: A non empty array is expected.', __METHOD__));
        }

        $this->fields = $fields;
        return $this;
    }

    public function validate(array $input)
    {
        if (empty($this->requiredFields) && empty($this->fields)) {
            throw new RuntimeException();
        }

        $input = $this->validateRequiredFields($input);
        $this->validateFields($input);
    }

    private function validateRequiredFields(array $input)
    {
        if (count(array_intersect_key($this->requiredFields, $input)) != count($this->requiredFields)) {
            throw new RuntimeException($this->getMessageForRequiredFields($input, $this->requiredFields));
        }

        return $input;
    }

    private function validateFields(array $input)
    {
        $this->requiredFields = array_merge($this->requiredFields, $this->fields);
        $filteredData = array_filter(filter_var_array($input, $this->requiredFields));

        if (count($filteredData) != count($input)) {
            throw new InvalidArgumentException($this->getMessageForInvalidFields($input, $filteredData));
        }

        return $input;
    }

    private function getMessageForInvalidFields($input, $filteredData)
    {
        $invalidFields = array_diff(array_keys($input), array_keys($filteredData));
        $messagePart = [];

        foreach ($invalidFields as $value) {
            $messagePart[] = sprintf('%s: %s', $value, $input[$value]);
        }

        return 'Invalid params: ' . implode(', ', $messagePart);
    }

    private function getMessageForRequiredFields($input, $requiredFields)
    {
        return 'Required params: ' . implode(', ', array_diff(array_keys($requiredFields), array_keys($input)));
    }
}
