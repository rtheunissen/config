<?php

namespace Concat\Config\Container\Test;

use Concat\Config\Container\Evaluator;
use Concat\Config\Container\Value;

class EvaluatorTest extends \PHPUnit_Framework_TestCase
{
    private function evaluate($value, $types)
    {
        $types = is_array($types) ? $types: [$types];
        return Evaluator::evaluate($value, $types);
    }

    public function object()
    {
        return new \stdClass();
    }

    public function validProvider()
    {
        return [

            // Basic types
            [Value::TYPE_STRING,    'valid'],
            [Value::TYPE_ARRAY,     [1, 2, 3]],
            [Value::TYPE_INTEGER,   10],
            [Value::TYPE_BOOLEAN,   true],
            [Value::TYPE_FLOAT,     1.5],
            [Value::TYPE_RESOURCE,  fopen('php://input', 'r')],
            [Value::TYPE_CLOSURE,   function(){}],
            [Value::TYPE_CALLABLE,  [$this, 'object']],
            [Value::TYPE_ARRAY,     [$this, 'object']],

            // callable is not invoked to produce object
            [[Value::TYPE_OBJECT, Value::TYPE_CALLABLE],  [$this, 'object']],

            // string -> expected types
            [Value::TYPE_FLOAT,     '5.5', 5.5],
            [Value::TYPE_BOOLEAN,   'false', false],
            [Value::TYPE_INTEGER,   '10', 10],

            // String takes precedence if value is string
            [[Value::TYPE_STRING, Value::TYPE_FLOAT], '5.5'],
            [[Value::TYPE_FLOAT, Value::TYPE_STRING], '5.5', 5.5],

            // integer -> expected types
            [Value::TYPE_BOOLEAN,    1, true],
            [Value::TYPE_FLOAT,      4, 4.0],
            [Value::TYPE_STRING,     5, "5"],

            // float -> expected types
            [Value::TYPE_BOOLEAN,    1.0, true],
            [Value::TYPE_INTEGER,      4.0, 4],
            [Value::TYPE_STRING,     5.0, "5"],

            // Extending types
            [get_class(new \A()), new \B()],

            // Closure evaluates to integer
            [Value::TYPE_INTEGER, function() { return 10; }, 10],

            // Callable array is invoked to produce instance
            ['stdClass', [$this, 'object'], new \stdClass()],

            // Acceptable closure is not invoked
            [['some class', Value::TYPE_CLOSURE], function(){}],

            // Closure is not invoked regardless of type order
            [[Value::TYPE_INTEGER, Value::TYPE_CLOSURE], function() { return 10; }],
            [[Value::TYPE_CLOSURE, Value::TYPE_INTEGER], function() { return 10; }],

            // Acceptable integer is valid even if closure type is expected
            [[Value::TYPE_INTEGER, Value::TYPE_CLOSURE], 10, 10],
        ];
    }

    public function invalidProvider()
    {
        return [

            // String doesn't match any basic types other than string
            [Value::TYPE_INTEGER,  'invalid'],
            [Value::TYPE_FLOAT,    'invalid'],
            [Value::TYPE_RESOURCE, 'invalid'],
            [Value::TYPE_CLOSURE,  'invalid'],

            // Integer and array are not valid closures
            [Value::TYPE_CLOSURE,  5],
            [Value::TYPE_CLOSURE,  []],
            [Value::TYPE_CLOSURE,  5.5],

            // Numeric string but no numeric expected type
            [Value::TYPE_OBJECT, '5.0'],

            // Array callable is not of type 'invalid'
            ['invalid', [$this, 'object']],

            // Resourse is not a valid integer
            [Value::TYPE_INTEGER, fopen('php://input', 'r')],

            // Trying to access 'a' on no values should indicate not found
            [[],  []],
        ];
    }

    /**
     * @dataProvider validProvider
     */
    public function testSuccessfulEvaluation($types, $value, $expected = null)
    {
        if ($expected === null) {
            $expected = $value;
        }
        $this->assertEquals($expected, $this->evaluate($value, $types));
    }

    /**
     * @dataProvider invalidProvider
     * @expectedException UnexpectedValueException
     */
    public function testUnsuccessfulEvaluation($types, $value)
    {
        $this->evaluate($value, $types);
    }
}
