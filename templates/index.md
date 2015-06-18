---
layout: default
permalink: /
title: Introduction
---

Introduction
============

[![Author](http://img.shields.io/badge/author-@{{ site.data.project.author }}-blue.svg)](https://twitter.com/{{ site.data.project.author }})
[![License](https://img.shields.io/packagist/l/{{ site.data.project.packagist }}.svg)](https://packagist.org/packages/{{ site.data.project.packagist }})
[![Latest Version](https://img.shields.io/packagist/v/{{ site.data.project.packagist }}.svg)](https://packagist.org/packages/{{ site.data.project.packagist }})
[![Build Status](https://img.shields.io/travis/{{ site.data.project.github }}.svg?branch=master)](https://travis-ci.org/{{ site.data.project.github }})
[![Scrutinizer](https://img.shields.io/scrutinizer/g/{{ site.data.project.github }}.svg)](https://scrutinizer-ci.com/g/{{ site.data.project.github }})
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/{{ site.data.project.github }}.svg)](https://scrutinizer-ci.com/g/{{ site.data.project.github }})

This library provides *containers for injected dependencies*, which is different to standard
dependency injection where containers inject the dependecies and the object is not aware of how it was instantiated. This library *does not* challenge these patterns, and is not an attempt to replace them.

Containers have expected types and optional defaults, and are constructed with a set of
dependency values which can be accessed on the container.

## What problem does this library try to solve?

This library aims to solve the problem where having many constructor parameters becomes hard to manage.
It provides a convenient way to handle parameter values and their defaults. In most cases, the alternative
is to do many manual `isset` and `is_a` checks, which these containers take care of for you.

## Use cases

### Type hinted parameters

We have a class that has two dependencies, both with default values.
We have to explicitly pass `null` as the first parameter to preserve its default
value when we set the second parameter to something else.

What's nice here is that it's pure - no libraries or any hard dependencies. Type validation
is handled by type hinting and it's easy enough to check parameter order in the documentation.

{% highlight php startinline=true %}

class Instance
{
    public function construct(CacheProvider $cache = null, StorageProvider $storage = null)
    {
        $this->cache = $cache ?: new Cache\Provider\MemoryCache();
        $this->storage = $storage ?: new Storage\Provider\SessionStorage();
    }
}

$instance = new Instance(null, new FileStorage());

{% endhighlight %}

### 'Options' style, single array parameter

There are many libraries that use an options-style, single array parameter constructor.
This is common when there are many parameters or where parameter to argument matching becomes difficult.

{% highlight php startinline=true %}

class Instance
{
    public function construct(array $options = [])
    {
        $defaults = [
            "cache"   => new Cache\Provider\MemoryCache(),
            "storage" => new Storage\Provider\SessionStorage(),
        ];

        $options = array_merge($defaults, $options);

        $this->cache   = $options['cache'];
        $this->storage = $options['storage'];
    }
}

$instance = new Instance([
    "storage" => new FileStorage(),
]);

{% endhighlight %}

An obvious problem here is that we may be creating more instances than we need to,
because we are directly instantiating all the default values. We would also need to
check that the value provided for the key matches the expected type.
