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

This library provides a flexible way to manage array-based configuration.
It makes it easy to specify expected value types and optional defaults. A container
is constructed using provided values, merged with any defined defaults, then
evaluates those values against their expected types as they are access for the first time.

## Features

- Multiple types
- Lazy evaluation
- Nested configuration

## Use cases

When you want to use a potentially nested array-based structure for configration with flexible types,
default values, lazy evaluation, and type validation.
