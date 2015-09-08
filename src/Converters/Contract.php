<?php

namespace DragonFly\Nag\Converters;

use Illuminate\Console\AppNamespaceDetectorTrait;

abstract class Contract
{

    use AppNamespaceDetectorTrait;

    protected $rules = [];
    protected $customAttributes = [];
    protected $formRequest = null;
    public $mapped_ids = [];

    public function init($formRequest = null)
    {
        // If a formRequest was defined as a string
        if ($formRequest != null && !is_object($formRequest)) {
            // If the class does not exist, namespace it.
            $class = ( class_exists($formRequest) ) ? $formRequest : $this->getAppNamespace() . 'Http\Requests\\' . $formRequest;
            $formRequest = new $class;
        }


        if ($formRequest) {
            // Retrieve the rules
            if (method_exists($formRequest, 'rules')) {
                $this->rules = $formRequest->rules();

                if (method_exists($formRequest, 'customAttributes')) {
                    $this->customAttributes = $formRequest->customAttributes();
                }
            }

            // Map the fields to their HTML ids
            if (method_exists($formRequest, 'map_html_ids')) {
                $this->mapped_ids = $formRequest->map_html_ids();
            }

            $this->formRequest = $formRequest;
        }
    }

    /**
     * Get the rules, format them to an array and convert them.
     *
     * @param $field
     *
     * @return array
     */
    public function retrieveRules($field)
    {
        $rules = [];

        // If rules were set for this field
        if (isset($this->rules[$field])) {

            // Make them array format if they were defined with pipes
            $raw_rules = ( is_array($this->rules[$field]) ) ?
                    $this->rules[$field] : explode('|', $this->rules[$field]);

            // Convert the rules and add the HTML id
            $rules = array_merge($this->convertRules($field, $raw_rules), ['id' => $this->getHtmlId($field)]);
        }

        return $rules;
    }

    /**
     * Make the rule's parameters an array, if they were defined with commas.
     *
     * @param $parameters
     *
     * @return array
     */
    protected function formatParameters($parameters)
    {
        if (!is_array($parameters)) {
            $parameters = explode(',', $parameters);
        }

        return $parameters;
    }

    /**
     * Get the HTML ID attribute for this field.
     *
     * @param $field
     *
     * @return mixed
     */
    public function getHtmlId($field)
    {
        return ( isset($this->mapped_ids[$field]) ) ? $this->mapped_ids[$field] : $field;
    }

    /**
     * Retrieve the field and rule's validation message.
     *
     * @param string $attribute
     * @param string $currentRule
     * @param array  $rules
     *
     * @return string
     */
    protected function getMessage($attribute, $currentRule, $rules)
    {
        $customKey = "validation.custom.{$attribute}.{$currentRule}";

        $customMessage = trans($customKey, ['attribute' => $this->getAttribute($attribute)]);

        if ($customMessage !== $customKey) {
            return $customMessage;
        } else if (in_array($currentRule, ['size', 'between', 'min', 'max'])) {
            $key = "validation.{$currentRule}." . $this->getValidationType($rules);

            return trans($key, ['attribute' => $this->getAttribute($attribute)]);
        }

        return trans('validation.' . $currentRule, ['attribute' => $this->getAttribute($attribute)]);
    }

    /**
     * Get the displayable name of the attribute.
     *
     * @param  string $attribute
     *
     * @return string
     */
    protected function getAttribute($attribute)
    {
        if (isset($this->customAttributes[$attribute])) {
            return $this->customAttributes[$attribute];
        }

        $key = "validation.attributes.{$attribute}";

        if (( $line = trans($key) ) !== $key) {
            return $line;
        } else {
            return str_replace('_', ' ', snake_case($attribute));
        }
    }

    /**
     * Rudimentary check for the validation data type.
     *
     * @param $rules
     *
     * @return string
     */
    protected function getValidationType($rules)
    {
        $type = 'string';

        array_filter($rules, function ($rule) use (&$type) {
            if (strpos($rule, 'integer') !== false || strpos($rule, 'numeric') !== false) {
                $type = 'numeric';
            }
            if (strpos($rule, 'string') !== false) {
                $type = 'string';
            }
            if (strpos($rule, 'image') !== false) {
                $type = 'file';
            }
            if (strpos($rule, 'array') !== false) {
                $type = 'array';
            }
        });

        return $type;
    }

    /**
     * Convert the rules for the specified field
     *
     * @param string $field Name of the field we want to parse the rules for
     * @param array  $rules All the field's rules
     *
     * @return array
     */
    public function convertRules($field, $rules)
    {

        if (ends_with($field, '_confirmation')) {
            return $this->getConfirmationRule($field);
        }

        $attrs = [];
        $fieldType = $this->getValidationType($rules);
        $date_format = null;

        foreach ($rules as $rule_string) {
            // Get the rule's name, parameters & message
            $parsed = explode(':', $rule_string);

            $rule = $parsed[0];
            $params = ( count($parsed) == 1 ) ? [] : $this->formatParameters($parsed[1]);
            $message = $this->getMessage($field, $rule, $rules);

            $attrs = array_merge($attrs, $this->mapRule($field, $rule, $params, $message, $fieldType));
        }

        return $attrs;
    }

    /**
     * Return rule for '_confirmation' field
     * 
     * @param string $field
     * @return type
     */
    public function getConfirmationRule($field)
    {
        $rule = "confirmed";
        $rules = [$rule];
        $attribute = substr($field, 0, strlen($field) - 13);
        $message = $this->getMessage($attribute, $rule, $rules);
        return $this->confirmationRule($attribute, $message);
    }

    /**
     * Return rule for '_confirmation' field for a given driver
     * 
     * @param string $attribute The main field name
     * @param string $message Validation error message
     * 
     * @return array
     */
    abstract protected function confirmationRule($attribute, $message);

    /**
     * Map a rule to the needed format.
     *
     * @param string $field     Name of the field
     * @param string $rule      Name of the rule
     * @param array  $params    Rule parameters
     * @param string $message   Validation error message
     * @param string $fieldType Type of field (string, numeric, array, file)
     *
     * @return array
     */
    abstract protected function mapRule($field, $rule, $params, $message, $fieldType);
}
