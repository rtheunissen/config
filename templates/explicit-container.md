---
layout: default
permalink: /explicit-container/
title: Explicit container
---

Explicit container
==================

An explicit container is less flexible than an implicit container, but offers more functionality in certain cases.
Dependencies are provided *already named*, so there is no need for the container to try match the instance to a name. This means that you can have multiple dependencies of the same type, and callables aren't evaluated until needed.

## Instantiating

There are two ways to create an explicit container:

  - Create one using a `ContainerFactory`
  - Create a class which extends `ExplicitContainer`

---

### Using a container factory

{% highlight php startinline=true %}
use Concat\Di\Factory\ContainerFactory;

// Expected dependency types
$types = [
    "example" => "Example\InstanceInterface",
];

// Optional default values
$defaults = [];

// Create a container factory, specifying expected types and defaults
$factory = new ContainerFactory($types, $defaults);

// Unnamed dependency values
$dependencies = [
    "example" => new Example\Instance,
];

// Create an explicit container by providing an array of dependency values
$container = $factory->createExplicitContainer($dependencies);

// Access the Example\Instance object by name
$example = $container->get('example');

{% endhighlight %}


### Creating a container class

{% highlight php startinline=true %}

use Concat\Di\Container\ExplicitContainer;

class Container extends ExplicitContainer
{
    /**
     * Returns an associative array where the keys are the names of the
     * dependencies and the values their corresponding class names.
     *
     * @return array an array mapping dependency names to expected class names
     */
    function types()
    {
        return [];
    }

    /**
     * Returns an associative array where the keys are the names of the
     * dependencies and the values their corresponding default values.
     *
     * @return array an array mapping dependency names to default values
     */
    function defaults()
    {
        return [];
    }
}

$container = Container::make($dependencies);

{% endhighlight %}

## Disadvantages

One significant disadvantage of the single array options-style constructor is that
it's not immediately obvious which parameters are required or expected. Make sure to
document these well, as autocompletion and reflection wouldn't work as expected.
