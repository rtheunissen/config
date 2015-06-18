---
layout: default
permalink: /basic-usage/
title: Basic usage
---

Basic usage
===========

## Creating a container

You will need to create a container by extending `Concat\Config\Container\AbstractContainer`.
This is where you will be defining your expected types and default values.

{% highlight php startinline=true %}
use Concat\Config\Container\AbstractContainer;

class Configuration extends AbstractContainer
{
    /**
     * Returns an associative array of expected types, with names mapped to
     * either a single type or an array of accepted types. These types can be
     * \Concat\Config\Container\Value constants or class names. You can also
     * specify `null` as an expected type, which allows null evaluations.
     */
    protected function getExpectedTypes()
    {
        return [

            // Amount of time before an HTTP request times out
            "timeout" => Value::TYPE_INTEGER,

            "logging" => [

                // PSR-3 logger implementation
                "logger" => "Psr\Log\LoggerInterface",

                // Log level to use for the logger
                "logLevel" => Value::TYPE_STRING,
            ],
        ];
    }

    /**
     * Returns an associative array of optional default values, with names
     * mapped to a single value. This value can also be a class name, which will
     * be evaluated when first requested. You can also specify callables which
     * will evaluate to their result when the value is requested, however this
     * does not work if a callable is also an acceptable value type.
     */
    protected function getDefaultValues()
    {
        return [

            // Amount of time before an HTTP request times out
            "timeout" => 10,

            "logging" => [

                // Log level to use for the logger
                "logLevel" => "debug",
            ],
        ];
    }
}
{% endhighlight %}

Use the static `make` method with provided values to create an instance of the container.

{% highlight php startinline=true %}
$container = Configuration::make([
    "logging" => [
        "logger" => function () {
            // Create instance of logger here, or set it directly
            // without using a Closure.
        }
    ],
]);
{% endhighlight %}

## Using a container

You should use the `get` method of the container to access its values. You can
specify multiple path arguments to access nested elements.

{% highlight php startinline=true %}
// This is now a constructed logger instance.
$logger = $container->get('logging', 'logger');

// 10
$timeout = $container->get('timeout');
{% endhighlight %}















## Using a container factory

The easiest way to use `rtheunissen/di` is by creating a `ContainerFactory`, which
allows you to specify the expected types and their optional default values without
the need to create a dedicated container class.

{% highlight php startinline=true %}
use Concat\Di\Factory\ContainerFactory;

$types = [
    "cache"  => "Doctrine\Common\Cache\CacheProvider",
    "client" => "GuzzleHttp\ClientInterface",
    "logger" => "Psr\Log\LoggerInterface",
];

$defaults = [
    "cache"  => "Doctrine\Common\Cache\ApcCache",
    "client" => "GuzzleHttp\Client",
];

// Create a container factory, passing expected types and defaults
$factory = new ContainerFactory($types, $defaults);

// Only specify a single dependency
$dependencies = [
    "logger" => new Monolog\Logger("example"),
];

// Automatically infer container type based on the dependencies
$container = $factory->createContainer($dependencies);

// Access a dependency directly on the container, in this case a
// lazily evaluated Doctrine\Common\Cache\ApcCache instance
$cache = $container['cache'];

{% endhighlight %}

## Extending an abstract container

If you need more flexibility, you can extend either an `ImplicitContainer` or an `ExplicitContainer`.
The only methods you need to implement are `types()` and `defaults()`. Both container types
extend `AbstractContainer`, and define their expected types and defaults the same way.

{% highlight php startinline=true %}
use Concat\Di\Container\ImplicitContainer;

class Container extends ImplicitContainer
{
    public function types()
    {
        return [
            "cache"  => "Doctrine\Common\Cache\CacheProvider",
            "client" => "GuzzleHttp\ClientInterface",
            "logger" => "Psr\Log\LoggerInterface",
        ];
    }

    public function defaults()
    {
        return [
            "cache"  => "Doctrine\Common\Cache\ApcCache",
            "client" => "GuzzleHttp\Client",
            "logger" => [$this, "getLogger"],
        ];
    }

    protected function getLogger()
    {
        return new Monolog\Logger("example");
    }
}

$container = Container::make($dependencies);
{% endhighlight %}
