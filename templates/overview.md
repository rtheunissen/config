---
layout: default
permalink: /overview/
title: Container overview
---

Container overview
==================

## Lazy loading

Both provided and default values are *evaluated* against their
expected type when accessed for the first time.

## Supported types

  - Objects
  - Class names
  - Callables that produce an expected type

  - `Value::TYPE_CLOSURE
  - `Value::TYPE_CALLABLE
  - `Value::TYPE_BOOLEAN
  - `Value::TYPE_INTEGER
  - `Value::TYPE_FLOAT
  - `Value::TYPE_STRING
  - `Value::TYPE_ARRAY
  - `Value::TYPE_RESOURCE
  - `Value::TYPE_OBJECT

## Mutiple types

Use an array to specify more than one expected type for a value. Use `null` as
an expected type if you would like to allow a value to be null.

{% highlight php startinline=true %}
protected function getExpectedTypes()
{
    return [

        // Amount of time before an HTTP request times out
        "timeout" => Value::TYPE_INTEGER,

        "logging" => [

            // PSR-3 logger implementation
            "logger" => ["Psr\Log\LoggerInterface", null],

            // Log level to use for the logger
            "logLevel" => [Value::TYPE_STRING, Value::TYPE_INTEGER],
        ],
    ];
}
{% endhighlight %}

## Value access

Dependencies can be accessed using the `get` method on the container. Use multiple
arguments to access nested elements.

{% highlight php startinline=true %}
$logger = $container->get('logging', 'logger');
{% endhighlight %}

## Validation

Dependency values are validated when they are evaluated (ie. lazy loaded). A depdendency is
considered valid if it matches an expected type or is a subclass of an expected type.
