---
layout: default
permalink: /container-overview/
title: Container overview
---

Container overview
==================

## Lazy loading

Both provided and default values are *evaluated* against their 
expected type when accessed for the first time. 

**Note:** Implicit containers are forced to evaluate callable values in order to determine
their type.

## Supported types

  - Objects
  - Class names
  - Callable arrays

  - `Dependency::TYPE_CLOSURE      // 'Closure'`
  - `Dependency::TYPE_BOOLEAN      // 'boolean'`
  - `Dependency::TYPE_INTEGER      // 'integer'`
  - `Dependency::TYPE_FLOAT        // 'double'`
  - `Dependency::TYPE_STRING       // 'string'`
  - `Dependency::TYPE_ARRAY        // 'array'`
  - `Dependency::TYPE_RESOURCE     // 'resource'`
  - `Dependency::TYPE_OBJECT       // 'object'`

## Value access

Dependencies can be accessed on a container using either array notation or `get`.

{% highlight php startinline=true %}
$cache = $container['cache'];

$cache = $container->get('cache');
{% endhighlight %}

## Validation

Dependency values are validated when they are evaluated (ie. lazy loaded). A depdendency is
considered valid if it matches the expected type or is a subclass of the expected type.
