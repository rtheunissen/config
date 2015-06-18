---
layout: default
permalink: /implicit-container/
title: Implicit container
---

Implicit container
==================

An implicit container automatically matches a given dependency by type to a defined name.
This removes the need to label dependencies when providing them, as well as the order in which
they are provided.

## Instantiating

There are two ways to create an implicit container:

  - Create one using a `ContainerFactory`
  - Create a class which extends `ImplicitContainer`

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
    new Example\Instance,
];

// Create an implicit container by providing an array of dependency values
$container = $factory->createImplicitContainer($dependencies);

// Access the Example\Instance object by name
$example = $container->get('example');

{% endhighlight %}

---

### Creating a container class

{% highlight php startinline=true %}

use Concat\Di\Container\ImplicitContainer;

class Container extends ImplicitContainer
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

---

## Behaviour notes

### Closures

It's possible to have a Closure as an expected dependency type. If the container
**does** expect a Closure and receives one, that Closure would then be matched
to the dependency that expected a Closure. However, if the container **does not**
expected a Closure and receives one, that Closure would then be evaluated.

### Multiple dependencies of the same type

Implicit containers don't work well for multiple dependencies of the same type.
A previously matched dependency would be overwritten by any dependencies of the
same type that follow.
