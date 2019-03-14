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
    use fn\Meta\Properties\ReadOnlyTrait;

    private $properties    = [];
    private $rawProperties = [];

    /**
     * @var self[]
     */
    private static $cache = [];

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (!fn\hasKey($name, $this->properties)) {
            $method = "resolve$name";
            if (method_exists($this, $method)) {
                $resolved = $this->$method(fn\at($name, $this->rawProperties, null));
            } else {
                $resolved = fn\at($name, $this->rawProperties);
            }
            $this->properties[$name] = $resolved instanceof Generator ? fn\traverse($resolved) : $resolved;
        }
        return $this->properties[$name];
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $properties = [])
    {
        $this->rawProperties = $properties;
        $this->init();
    }

    protected function init(): void
    {
    }

    public function add(string $property, ...$lines): self
    {
        $this->__get($property);
        foreach ($lines as $line) {
            if (!$line || !fn\hasValue($line, $this->$property)) {
                $this->properties[$property][] = $line;
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
        return new static(array_merge(self::$DEFAULT ?? [], $properties, ['name' => $name]));
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
