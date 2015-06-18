<?php

namespace Concat\Config\Container\Test;

use Concat\Config\Container\AbstractContainer;

class MockContainer extends AbstractContainer
{
    public static $_types;
    public static $_defaults;

    protected function getExpectedTypes()
    {
        return self::$_types;
    }

    protected function getDefaultValues()
    {
        return self::$_defaults;
    }
}
