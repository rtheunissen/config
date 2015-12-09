<?php

namespace Concat\Config\Container\Test;

use Concat\Config\Container\Value;

class AbstractContainerTest extends \PHPUnit_Framework_TestCase
{

    public function providerTestValidGet()
    {
        $a = new \A();

        return [

            // All provided, no defaults
            [
                ['a' => get_class($a)],
                [],
                ['a' => $a],
            ],

            // None provided, all defaults
            [
                ['a' => get_class($a)],
                ['a' => $a],
                [],
            ],

            // Nested defaults
            [
                ['a' => ['b' => ['c' => get_class($a)]]],
                ['a' => ['b' => ['c' => $a]]],
                [],
                ['a', 'b', 'c'],
            ],

            // Nested provided
            [
                ['a' => ['b' => ['c' => get_class($a)]]],
                [],
                ['a' => ['b' => ['c' => $a]]],
                ['a', 'b', 'c'],
            ],

            // Partial nested defaults
            [
                ['a' => ['b' => ['c' => get_class($a)]]],
                ['a' => ['b' => []]],
                ['a' => ['b' => ['c' => $a]]],
                ['a', 'b', 'c'],
            ],

            // Lazy loaded defaults
            [
                ['a' => get_class($a)],
                ['a' => get_class($a)],
                [],
            ],

            // Nested lazy loaded defaults
            [
                ['a' => ['b' => ['c' => get_class($a)]]],
                ['a' => ['b' => ['c' => get_class($a)]]],
                [],
                ['a', 'b', 'c'],
            ],
        ];
    }

    /**
     * @dataProvider providerTestValidGet
     */
    public function testValidGet($types, $defaults, $provided, $key=null)
    {
        $key = $key ?: ['a'];

        MockContainer::$_types = $types;
        MockContainer::$_defaults = $defaults;

        $container = MockContainer::make($provided);

        $this->assertInstanceOf(
            get_class(new \A()),
            call_user_func_array([$container, 'get'], $key)
        );
    }

    public function expectedArrayProvider()
    {
        return [

            // Expected array yields provided array
            [
                ['a' => Value::TYPE_ARRAY],
                [],
                ['a' => []]
            ],

            // Expected array yields default array
            [
                ['a' => Value::TYPE_ARRAY],
                ['a' => []],
                [],
            ],

            // Nested expected array yields provided array
            [
                ['a' => ['b' => Value::TYPE_ARRAY]],
                [],
                ['a' => ['b' => []]],
                ['a', 'b']
            ],

            // Nested expected array yields default array
            [
                ['a' => ['b' => Value::TYPE_ARRAY]],
                ['a' => ['b' => []]],
                [],
                ['a', 'b'],
            ],
        ];
    }

    /**
     * @dataProvider expectedArrayProvider
     */
    public function testExpectedArray($types, $defaults, $provided, $key=['a'])
    {
        MockContainer::$_types = $types;
        MockContainer::$_defaults = $defaults;

        $container = MockContainer::make($provided);
        $this->assertInternalType(
            'array',
            call_user_func_array([$container, 'get'], $key)
        );
    }

    public function expectedNullProvider()
    {
        return [

            // Multi type null yields allowed provided null
            [
                ['a' => [Value::TYPE_ARRAY, null]],
                [],
                ['a' => null],
            ],

            // Multi type null yields allowed default null
            [
                ['a' => [Value::TYPE_ARRAY, null]],
                ['a' => null],
                [],
            ],

            // Multi type null yields allowed provided nested null
            [
                ['a' => ['b' => [Value::TYPE_ARRAY, null]]],
                [],
                ['a' => ['b' => null]],
                ['a', 'b']
            ],

            // Multi type null yields allowed default nested null
            [
                ['a' => ['b' => [Value::TYPE_ARRAY, null]]],
                ['a' => ['b' => null]],
                [],
                ['a', 'b'],
            ],
        ];
    }

    /**
     * @dataProvider expectedNullProvider
     */
    public function testExpectedNull($types, $defaults, $provided, $key=['a'])
    {
        MockContainer::$_types = $types;
        MockContainer::$_defaults = $defaults;

        $container = MockContainer::make($provided);
        $this->assertNull(call_user_func_array([$container, 'get'], $key));
    }

    public function providerTestInvalidGet()
    {
        $a = new \A();
        $b = new \B();

        return [

            // Nested types require nested provided
            [
                ['a' => ['b' => ['c' => get_class($a)]]],
                [],
                ['a' => $a],
                ['a', 'b', 'c'],
            ],

            // Bad default value
            [
                ['a' => get_class($a)],
                ['a' => 10],
                [],
            ],

            // Bad provided value
            [
                ['a' => get_class($a)],
                [],
                ['a' => 10],
            ],

            // Requesting bad name
            [
                [],
                [],
                [],
            ],

            // Type not provided
            [
                [],
                [],
                ['a' => 10],
            ],

        ];
    }

    /**
     * @dataProvider providerTestInvalidGet
     * @expectedException Exception
     */
    public function testInvalidGet($types, $defaults, $instances, $key=null)
    {
        $key = $key ?: ['a'];

        MockContainer::$_types = $types;
        MockContainer::$_defaults = $defaults;

        $container = MockContainer::make($instances);

        call_user_func_array([$container, 'get'], $key);
    }

    public function testDotNotation()
    {
        MockContainer::$_types = [
            'foo' => [
                'bar' => Value::TYPE_INTEGER,
            ],
        ];

        MockContainer::$_defaults = [
            'foo' => [
                'bar' => 123,
            ],
        ];

        $container = MockContainer::make();

        $this->assertEquals(123, $container->get('foo.bar'));

        $container = MockContainer::make([
            'foo' => [
                'bar' => 456,
            ],
        ]);

        $this->assertEquals(456, $container->get('foo.bar'));
    }
}

