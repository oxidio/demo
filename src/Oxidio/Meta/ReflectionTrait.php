<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;
use Generator;

/**
 * @property-read string $name
 */
trait ReflectionTrait
{
    use fn\PropertiesReadOnlyTrait;

    private $resolved = [];

    /**
     * @var self[]
     */
    private static $cache = [];

    /**
     * @inheritdoc
     * @return mixed
     */
    protected function property(string $name, bool $assert)
    {
        if (!fn\hasKey($name, $this->resolved)) {
            $method = "resolve$name";
            if (method_exists($this, $method)) {
                $value = $this->$method($this->properties[$name] ?? null);
            } else {
                $value = fn\at($name, $this->properties, ...($assert ? [] : [null]));
            }
            $this->resolved[$name] = $value instanceof Generator ? fn\traverse($value) : $value;
        }
        return $assert ? $this->resolved[$name] : true;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $properties = [])
    {
        $this->initProperties($properties);
        $this->init();
    }

    /**
     * @param array $args
     * @return fn\Map|self[]
     */
    public static function cached(...$args): fn\Map
    {
        return fn\map(self::$cache, ...$args);
    }

    protected function init(): void
    {
    }

    public function add(string $property, ...$lines): self
    {
        $this->__get($property);
        foreach ($lines as $line) {
            if (!$line || !fn\hasValue($line, $this->$property)) {
                $this->resolved[$property][] = $line;
            }
        }
        return $this;
    }

    /**
     * @param string $name
     * @param array $properties
     *
     * @return static
     */
    public static function get(string $name, array $properties = []): self
    {
        return self::$cache[$name] ?? self::$cache[$name] = self::create($name, $properties);
    }

    public static function create(string $name, array $properties = []): self
    {
        return new static(array_merge($properties, ['name' => $name]));
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public static function export(): void
    {
    }
}
