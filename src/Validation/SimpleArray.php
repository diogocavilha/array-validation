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
    private $validFields = [];
    private $fieldsToRemove = [];
    private $messages = [];

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

    private function checkFieldsToRemove(array $allfields)
    {
        foreach ($this->fieldsToRemove as $field) {
            $this->checkIfFieldIsAboutToBeValidated($field, $allfields);
        }
    }

    private function checkIfFieldIsAboutToBeValidated($field, array $allfields)
    {
        if (array_key_exists($field, $allfields)) {
            throw new RuntimeException(
                sprintf('Cannot remove the field "%s". This field is about to be validated.', $field)
            );
        }
    }

    private function getFieldsToRemove(array $input)
    {
        $allfields = array_merge($this->requiredFields, $this->fields);

        if (!empty($this->fieldsToRemove)) {
            $this->checkFieldsToRemove($allfields);
            return $this->fieldsToRemove;
        }

        return array_diff(array_keys($input), array_keys($allfields));
    }

    private function getInputWithoutUnwantedFields(array $input)
    {
        $fieldsToRemove = $this->getFieldsToRemove($input);

        foreach ($fieldsToRemove as $field) {
            unset($input[$field]);
        }

        return $input;
    }

    private function validateParameters()
    {
        if (empty($this->requiredFields) && empty($this->fields)) {
            throw new RuntimeException('There are no fields for validating.');
        }
    }

    private function recreateFieldsFromInput(array $input)
    {
        foreach ($this->fields as $key => $value) {
            if (!array_key_exists($key, $input)) {
                unset($this->fields[$key]);
            }
        }
    }

    public function validate(array $input)
    {
        $this->validateParameters();

        $input = $this->getInputWithoutUnwantedFields($input);

        $this->recreateFieldsFromInput($input);

        $this->validateRequiredFields($input);
        $this->validateFields($input);

        return $this;
    }

    private function getRequiredFieldsFromInput(array $input)
    {
        return array_intersect_key($this->requiredFields, $input);
    }

    private function validateRequiredFields(array $input)
    {
        if (count($this->getRequiredFieldsFromInput($input)) != count($this->requiredFields)) {
            throw new RuntimeException($this->getMessageForRequiredFields($input, $this->requiredFields));
        }
    }

    private function validateRequiredFieldsForMessage(array $input)
    {
        if (count($this->getRequiredFieldsFromInput($input)) != count($this->requiredFields)) {
            $this->createMessageForRequiredFields($input, $this->requiredFields);
        }
    }

    private function applyFilterTo(array $input)
    {
        $this->requiredFields = array_merge($this->requiredFields, $this->fields);
        $filtered = array_filter(filter_var_array($input, $this->requiredFields));
        $fieldsDiff = array_diff(array_keys($input), array_keys($filtered));

        foreach ($fieldsDiff as $field) {
            $filtered = $this->addNotRemovedFields($input, $filtered, $field);
        }

        return $filtered;
    }

    private function addNotRemovedFields(array $input, array $filtered, $field)
    {
        if (!in_array($field, array_keys($this->requiredFields))) {
            $filtered[$field] = $input[$field];
            $this->requiredFields[$field] = $input[$field];
        }

        return $filtered;
    }

    private function validateFields(array $input)
    {
        $filteredData = $this->applyFilterTo($input);

        if (count($filteredData) != count($this->requiredFields)) {
            throw new InvalidArgumentException($this->getMessageForInvalidFields($input, $filteredData));
        }

        $this->validFields = $filteredData;
    }

    private function validateFieldsForMessage(array $input)
    {
        $filteredData = $this->applyFilterTo($input);

        if (count($filteredData) != count($this->requiredFields)) {
            $this->createMessageForInvalidFields($input, $filteredData);
            return;
        }

        $this->validFields = $filteredData;
    }

    private function getMessageForInvalidFields($input, $filteredData)
    {
        $invalidFields = array_diff(array_keys($input), array_keys($filteredData));
        $fieldsLog = [];

        foreach ($invalidFields as $field) {
            $fieldsLog[] = sprintf('%s: %s', $field, $input[$field]);
        }

        return 'Invalid params: ' . implode(', ', $fieldsLog);
    }

    private function createMessageForInvalidFields($input, $filteredData)
    {
        $invalidFields = array_diff(array_keys($input), array_keys($filteredData));

        foreach ($invalidFields as $field) {
            $this->messages[] = sprintf('Field "%s" with value "%s" is not valid.', $field, $input[$field]);
        }
    }

    private function getMessageForRequiredFields($input, $requiredFields)
    {
        return 'Required params: ' . implode(', ', array_diff(array_keys($requiredFields), array_keys($input)));
    }

    private function createMessageForRequiredFields(array $input, array $requiredFields)
    {
        $invalidFields = array_diff(array_keys($requiredFields), array_keys($input));

        foreach ($invalidFields as $field) {
            $this->messages[] = sprintf('Field "%s" is required.', $field);
        }
    }

    public function getValidArray()
    {
        return $this->validFields;
    }

    public function removeOnly(array $fields)
    {
        $this->fieldsToRemove = $fields;
        return $this;
    }

    public function isValid(array $input)
    {
        $this->validateParameters();

        $input = $this->getInputWithoutUnwantedFields($input);

        $this->recreateFieldsFromInput($input);

        $this->validateRequiredFieldsForMessage($input);
        $this->validateFieldsForMessage($input);

        if (!empty($this->messages)) {
            return false;
        }

        return true;
    }

    public function getMessages()
    {
        return $this->messages;
    }
}
