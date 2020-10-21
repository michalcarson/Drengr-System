<?php

namespace Drengr\Framework;

use InvalidArgumentException;

class Validator
{
    protected $rules = [];
    protected $errors = [];
    protected $inputData;

    /**
     * Assign rules to validate an attribute.
     *
     * Rules should be an array of strings. Each entry describes one rule:
     *    required
     *    string
     *    integer
     *    bool
     *    url
     *    email
     *
     * @param string $attribute
     * @param array $rules
     */
    public function setRules(string $attribute, array $rules)
    {
        $this->rules[$attribute] = $this->validateRules($rules);
    }

    /**
     * Run the validation rules against the input data. Returns true if the data appears valid.
     *
     * @param array $inputData
     * @return bool
     */
    public function valid(array $inputData)
    {
        $this->errors = [];
        $this->inputData = $this->extractData($inputData);

        foreach ($this->rules as $attribute => $rules) {
            $value = isset($this->inputData[$attribute]) ? $this->inputData[$attribute] : null;

            foreach ($rules as $rule) {
                $this->runRule($attribute, $rule, $value);
            }
        }

        if (count($this->errors)) {
            return false;
        }

        return true;
    }

    /**
     * Sanitize the data.
     *
     * Runs a sanitizer routine against each attribute based on the rules for that attribute.
     *
     * @param array $data
     * @return array
     */
    public function sanitize(array $data)
    {
        $sanitized = $this->extractData($data);

        foreach ($this->rules as $attribute => $rules) {
            $value = isset($sanitized[$attribute]) ? $sanitized[$attribute] : null;

            foreach ($rules as $rule) {
                $sanitized[$attribute] = $this->runSanitizer($attribute, $rule, $value);
            }
        }

        return $sanitized;
    }

    /**
     * Return the list of errors that occurred when the rules were run.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Extract data from the input array.
     *
     * Extract a value from the input array for each attribute defined in the rules. If an attribute is
     * required but not found in the input array, it will not be defined in the extracted data.
     *
     * @param array $data
     * @return array
     */
    protected function extractData(array $data)
    {
        $validatable = [];

        foreach ($this->rules as $attribute => $rules) {
            if (in_array('required', $rules)) {
                if ( ! is_null($data[$attribute])) {
                    $validatable[$attribute] = $data[$attribute];
                }
            } else {
                $validatable[$attribute] = isset($data[$attribute]) ? $data[$attribute] : null;
            }
        }

        return $validatable;
    }

    /**
     * Run a single validation rule against a passed value.
     *
     * @param string $attribute
     * @param string $rule
     * @param $value
     */
    protected function runRule(string $attribute, string $rule, $value)
    {
        $methodName = $this->getMethodName($rule);
        if (method_exists($this, $methodName)) {
            $result = $this->$methodName($attribute, $value);
        } else {
            $result = sprintf('%s validation method is not available.', $rule);
        }

        if ($result !== true) {
            $this->errors[] = $result;
        }
    }

    /**
     * Run a single sanitizer against a passed value.
     *
     * @param string $attribute
     * @param string $rule
     * @param $value
     * @return mixed
     */
    protected function runSanitizer(string $attribute, string $rule, $value)
    {
        $methodName = $this->getSanitizeMethodName($rule);
        if (method_exists($this, $methodName)) {
            return $this->$methodName($attribute, $value);
        }

        // If there is no sanitizer method, return the value unchanged.
        return $value;
    }

    /**
     * Validate that all rules requested are known to this validator class.
     *
     * @param array $rules
     * @return array
     * @throws InvalidArgumentException
     */
    protected function validateRules(array $rules)
    {
        $invalid = [];

        foreach ($rules as $rule) {
            $methodName = $this->getMethodName($rule);
            if ( ! method_exists($this, $methodName)) {
                $invalid[] = $rule;
            }
        }

        if (empty($invalid)) {
            return $rules;
        }

        throw new InvalidArgumentException(
            sprintf("Invalid rules encountered: '%s'", implode("', '", $invalid))
        );
    }

    /**
     * Return the name of a method corresponding the validation rule.
     *
     * @param $rule
     * @return string
     */
    protected function getMethodName($rule)
    {
        return 'check' . ucfirst($rule);
    }

    /**
     * Return the name of a sanitization method for the given validation rule.
     *
     * @param $rule
     * @return string
     */
    protected function getSanitizeMethodName($rule)
    {
        return 'sanitize' . ucfirst($rule);
    }

    /**
     * Confirm that a required attribute has been provided. This method inspects the
     * class level `inputData` array instead of using the passed value.
     *
     * @param $attribute
     * @param $value
     * @return bool|string
     */
    public function checkRequired($attribute, $value)
    {
        if (empty($this->inputData[$attribute])) {
            return sprintf('%s is required.', $attribute);
        }

        return true;
    }

    /**
     * Confirm that the passed value is a string.
     *
     * @param $attribute
     * @param $value
     * @return bool|string
     */
    public function checkString($attribute, $value)
    {
        if ( ! empty($value) && ! is_string($value)) {
            return sprintf('%s must be a string.', $attribute);
        }

        return true;
    }

    /**
     * Confirm that a passed value is an integer.
     *
     * @param $attribute
     * @param $value
     * @return bool|string
     */
    public function checkInteger($attribute, $value)
    {
        if ( ! empty($value) && ! is_integer($value)) {
            return sprintf('%s must be an integer.', $attribute);
        }

        if ( ! empty($value) && preg_match('/[0-9]+/', $value) !== 1) {
            return sprintf('%s must be an integer (regex).', $attribute);
        }

        return true;
    }

    /**
     * Confirm that a passed value is boolean.
     *
     * @param $attribute
     * @param $value
     * @return bool|string
     */
    public function checkBool($attribute, $value)
    {
        if ( ! empty($value) && ! is_bool($value)) {
            return sprintf('%s must be a boolean.', $attribute);
        }

        return true;
    }

    /**
     * Confirm that a passed value is a float (or decimal or real).
     *
     * @param $attribute
     * @param $value
     * @return bool|string
     */
    public function checkFloat($attribute, $value)
    {
        if ( ! empty($value) && ! is_float($value)) {
            return sprintf('%s must be a float.', $attribute);
        }

        return true;
    }

    /**
     * Confirm that passed value is a URL.
     *
     * @param $attribute
     * @param $value
     * @return bool|string
     */
    public function checkUrl($attribute, $value)
    {
        $parts = parse_url($value);
        if ($parts === false || ! is_array($parts)) {
            return sprintf('%s must be a valid URL.', $attribute);
        }

        return true;
    }

    /**
     * Confirm that a passed value is an email address.
     *
     * @param $attribute
     * @param $value
     * @return bool|string
     */
    public function checkEmail($attribute, $value)
    {
        // `is_email` is in the file wp-includes/formatting.php
        if ( ! empty($value) && ! is_email($value)) {
            return sprintf('%s must be a valid email address.', $attribute);
        }

        return true;
    }

    public function sanitizeString($value)
    {
        return sanitize_text_field($value);
    }

    public function sanitizeEmail($value)
    {
        return sanitize_email($value);
    }

    public function sanitizeInteger($value)
    {
        return (int)$value;
    }

    public function sanitizeBoolean($value)
    {
        return (bool)$value;
    }

    public function sanitizeFloat($value)
    {
        return (float)$value;
    }
}
