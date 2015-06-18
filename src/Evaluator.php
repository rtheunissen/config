<?php

namespace Concat\Config\Container;

use UnexpectedValueException;

/**
 * Used to evaluate a value according to a list of expected types.
 */
class Evaluator
{

    /**
     * Evaluates a value according to a list of expected types.
     *
     * @param mixed $value The value to evaluate.
     * @param array $types The expected types of the evaluated value.
     *
     * @return mixed
     *
     * @throws UnexpectedValueException if the value could not be evaluated.
     */
    public static function evaluate($value, array $types)
    {
        $valuetype = gettype($value);

        if (in_array($valuetype, $types)) {
            return $value;
        }

        foreach ($types as $type) {

            // No direct match, delegate by type
            $result = self::delegate($value, $type, $valuetype, $types);

            if ($result !== null) {
                return $result;
            }
        }

        // Value could not be evaluated to any of the expected types.
        throw new UnexpectedValueException(sprintf(
            "Could not evaluate value for expected type: %s",
            implode(',', $types)
        ));
    }

    /**
     * Calls an evaluation function based on the type of the value.
     *
     * @param mixed  $value The value to evaluate.
     * @param string $type The expected type of the evaluated value.
     * @param string $valuetype The raw type of the value to evaluate.
     * @param array  $types Acceptable value types.
     *
     * @return mixed|null The evaluated value or null if failed to evaluate.
     */
    private static function delegate($value, $type, $valuetype, $types)
    {
        switch ($valuetype) {
            case Value::TYPE_ARRAY:
            case Value::TYPE_OBJECT:
            case Value::TYPE_STRING:
            case Value::TYPE_INTEGER:
            case Value::TYPE_FLOAT:
                return self::{"evaluate".$valuetype}($value, $type, $types);
        }
    }

    /**
     * Attempts to evaluate a string according to an expected type.
     *
     * @param string $value The value to evaluate.
     * @param string $type The expected type of the evaluated value.
     * @param array  $types Acceptable value types.
     *
     * @return mixed|null The evaluated value or null if failed to evaluate.
     */
    private static function evaluateString($value, $type, $types)
    {
        if (class_exists($value)) {
            return self::evaluateObject(new $value, $type, $types);
        }

        return self::evaluateUnmatchedString($value, $type, $types);
    }

    /**
     * Attempts to evaluate a string according to an expected type, knowing
     * that the expected type was not a string.
     *
     * @param string $value The value to evaluate.
     * @param string $type The expected type of the evaluated value.
     *
     * @return mixed|null The evaluated value or null if failed to evaluate.
     */
    private static function evaluateUnmatchedString($value, $type)
    {
        if ($type === Value::TYPE_BOOLEAN) {

            // This is better than a boolean cast, as (bool)"false" is true.
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        // Check if numeric first otherwise anything evaluates to 0.
        if (is_numeric($value)) {

            if ($type === Value::TYPE_INTEGER) {
                return intval($value);
            }

            if ($type === Value::TYPE_FLOAT) {
                return floatval($value);
            }
        }
    }

    /**
     * Attempts to evaluate an integer according to an expected type, knowing
     * that the expected type was not an integer.
     *
     * @param string $value The value to evaluate.
     * @param string $type The expected type of the evaluated value.
     *
     * @return mixed|null The evaluated value or null if failed to evaluate.
     */
    private static function evaluateInteger($value, $type)
    {
        switch ($type) {
            case Value::TYPE_BOOLEAN:
                return (bool) $value;
            case Value::TYPE_FLOAT:
                return floatval($value);
            case Value::TYPE_STRING:
                return "$value";
        }
    }

    /**
     * Attempts to evaluate a float according to an expected type, knowing
     * that the expected type was not an float.
     *
     * @param string $value The value to evaluate.
     * @param string $type The expected type of the evaluated value.
     *
     * @return mixed|null The evaluated value or null if failed to evaluate.
     */
    private static function evaluateDouble($value, $type)
    {
        switch ($type) {
            case Value::TYPE_BOOLEAN:
                return (bool) $value;
            case Value::TYPE_INTEGER:
                return intval($value);
            case Value::TYPE_STRING:
                return "$value";
        }
    }

    /**
     * Attempts to evaluate an object according to an expected type.
     *
     * @param object $value The value to evaluate.
     * @param string $type The expected type of the evaluated value.
     * @param array  $types Acceptable value types.
     *
     * @return mixed|null The evaluated value or null if failed to evaluate.
     */
    private static function evaluateObject($value, $type, $types)
    {
        if (is_a($value, $type)) {
            return $value;
        }

        if (is_a($value, Value::TYPE_CLOSURE)) {

            // Check if a closure is at all acceptable before evaluating.
            if (in_array(Value::TYPE_CLOSURE, $types)) {
                return $value;
            }

            return self::evaluate($value(), [$type]);
        }
    }

    /**
     * Attempts to evaluate an object according to an expected type.
     *
     * @param array $value The value to evaluate.
     * @param string $type The expected type of the evaluated value.
     * @param array  $types Acceptable value types.
     *
     * @return mixed|null The evaluated value or null if failed to evaluate.
     */
    private static function evaluateArray($value, $type, $types)
    {
        if (is_callable($value)) {

            // Check if a callable is at all acceptable before evaluating.
            if (in_array(Value::TYPE_CALLABLE, $types)) {
                return $value;
            }

            return self::evaluate($value(), [$type]);
        }
    }
}
