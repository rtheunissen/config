<?php

namespace Concat\Config\Container;

use Concat\Config\Container\Value;

use Concat\Config\Container\Exception\DependencyNotFoundException;
use Concat\Config\Container\Exception\TypeNotDefinedException;
use Concat\Config\Container\Exception\DependencyEvaluationException;

use InvalidArgumentException;

/**
 * Abstract configration container used to manage a configuration array.
 */
abstract class AbstractContainer
{
    /**
     * @var mixed[] Provided configuration values.
     */
    protected $provided;

    /**
     * @var mixed[] Evaluated configration values.
     */
    protected $evaluated = [];

    /**
     * @var array Expected configration value types.
     */
    private $types;

    /**
     * @var array Default configration values.
     */
    private $defaults;

    /**
     * Returns an associative array of expected types, with names mapped to
     * either a single type or an array of accepted types. These types can be
     * \Concat\Config\Container\Value constants or class names. You can also
     * specify `null` as an expected type, which allows null evaluations.
     *
     * eg.
     * return [
     *     'a' => Value::TYPE_INTEGER,
     *     'b' => [
     *         'c' => "\Class\Path",
     *     ],
     * ];
     *
     * @return array
     */
    abstract protected function getExpectedTypes();

    /**
     * Returns an associative array of optional default values, with names
     * mapped to a single value. This value can also be a class name, which will
     * be evaluated when first requested. You can also specify callables which
     * will evaluate to their result when the value is requested, however this
     * does not work if a callable is also an acceptable value type.
     *
     * eg.
     * return [
     *     'a' => 10,
     *     'b' => [
     *         'c' => new \Class\Path(),
     *     ],
     * ];
     *
     */
    abstract protected function getDefaultValues();

    /**
     * Constructs this configuration container. Public access is not allowed as
     * a container should not be able to be re-constructed. Use ::make instead.
     *
     * @param mixed[] $provided
     */
    protected function __construct(array $provided)
    {
        $this->types    = $this->getExpectedTypes();
        $this->defaults = $this->getDefaultValues();

        $this->provided = array_merge_recursive($this->defaults, $provided);
    }

    /**
     * Creates an instance of this container using the provided values.
     *
     * @param array $provided
     *
     * @return AbstractContainer
     */
    public static function make(array $provided = [])
    {
        return new static($provided);
    }

    /**
     * Attempts to find a value by descending into an array, following a given
     * key path.
     *
     * @param array $haystack
     * @param array $levels
     *
     * @return mixed
     *
     * @throws InvalidArgumentException if the path is not valid
     */
    private function find(array $haystack, array $path)
    {
        foreach ($path as $key) {
            if ( ! array_key_exists($key, $haystack)) {
                throw new InvalidArgumentException(
                    "Could not find value for " . implode('.', $path)
                );
            }

            $haystack = $haystack[$key];
        }

        // Should be the value we are looking for
        return $haystack;
    }

    /**
     * Finds the expected types for a given key path.
     *
     * @param array $path
     *
     * @return array
     *
     * @throws InvalidArgumentException if the types could not be found.
     */
    protected function findTypes(array $path)
    {
        $types = $this->find($this->types, $path);
        return is_array($types) ? $types : [$types];
    }

    /**
     * Finds the configuration value for a given key path.
     *
     * @param array $path
     *
     * @return mixed
     *
     * @throws InvalidArgumentException if the value could not be found.
     */
    protected function findValue(array $path)
    {
        return $this->find($this->provided, $path);
    }

    /**
     * Evaluates a configuration value according to an array of expected types.
     *
     * @param mixed $value The value to evaluate.
     * @param array $types The expected types for the value.
     *
     * @return mixed The evaluated configuration value.
     *
     * @throws UnexpectedValueException if the value doesn't match.
     */
    protected function evaluate($value, array $types)
    {
        if ($value === null && in_array($value, $types)) {
            return $value;
        }

        return Evaluator::evaluate($value, $types);
    }

    /**
     * Returns a configuration value defined by a path provided as arguments.
     *
     * @return mixed
     */
    public function get()
    {
        $path = func_get_args();
        $key  = implode('.', $path);

        if ( ! array_key_exists($key, $this->evaluated)) {

            $value = $this->findValue($path);
            $types = $this->findTypes($path);

            $this->evaluated[$key] = $this->evaluate($value, $types);
        }

        return $this->evaluated[$key];
    }
}