---
layout: default
permalink: /basic-usage/
title: Basic usage
---

Basic usage
===========


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
