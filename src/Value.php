<?php

namespace Concat\Config\Container;

/**
 * Constants used to indicate acceptable value types.
 */
class Value
{
    /**
     * @var string A constant that indicates an expected boolean value.
     */
    const TYPE_BOOLEAN = "boolean";

    /**
     * @var string A constant that indicates an expected integer value.
     */
    const TYPE_INTEGER = "integer";

    /**
     * @var string A constant that indicates an expected double value.
     */
    const TYPE_FLOAT = "double";

    /**
     * @var string A constant that indicates an expected string value.
     */
    const TYPE_STRING = "string";

    /**
     * @var string A constant that indicates an expected array value.
     */
    const TYPE_ARRAY = "array";

    /**
     * @var string A constant that indicates an expected resource value.
     */
    const TYPE_RESOURCE = "resource";

    /**
     * @var string A constant that indicates an expected Closure value.
     */
    const TYPE_CLOSURE = "Closure";

    /**
     * @var string A constant that indicates an expected callable value.
     */
    const TYPE_CALLABLE = "callable";

    /**
     * @var string A constant that indicates an expected object value.
     */
    const TYPE_OBJECT = "object";
}
