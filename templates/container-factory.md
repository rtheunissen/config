---
layout: default
permalink: /container-factory/
title: Container factory
---

Container factory
=================

A container factory allows you to specify the expected types and their optional default values without
the need to create a dedicated container class. This is useful when you might want to use both an implicit
and explicit container, but you don't want to create two effectively identical classes.

## Creating a factory

You can create a `Concat\Di\Factory\ContainerFactory` directly by providing an array of expected types
and an optional array of default values. The factory is then ready to produce a dependency container.

{% highlight php startinline=true %}
$types = [
    "cache"  => "Doctrine\Common\Cache\CacheProvider",
    "client" => "GuzzleHttp\ClientInterface",
    "logger" => "Psr\Log\LoggerInterface",
];

$defaults = [
    "cache"  => "Doctrine\Common\Cache\ApcCache",
    "client" => "GuzzleHttp\Client",
];

$factory = new ContainerFactory($types, $defaults);
{% endhighlight %}

## Using a factory to create a container

To create a container, call `createContainer()` on a factory, which will produce the
most appropriate container based on the arguments it receives. If it receives
an associative array, it'll produce an `ExplicitContainer`, otherwise a value array will produce an `ImplicitContainer`. This gives you the option to define dependencies
as either a list of values, or an options-style associative array.

{% highlight php startinline=true %}
// Creates an implicit container
$container = $factory->createContainer([
    new Doctrine\Common\Cache\ApcCache(),
]);

// Creates an explicit container
$container = $factory->createContainer([
    "cache" => new Doctrine\Common\Cache\ApcCache(),
]);
{% endhighlight %}

You can also call `createExplicitContainer` or `createImplicitContainer` directly.
