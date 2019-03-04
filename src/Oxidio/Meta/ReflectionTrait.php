<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;

/**
 * @property-read string $name
 */
trait ReflectionTrait
{
    use fn\Meta\Properties\ReadOnlyTrait;

    private $properties = [];

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
            $this->properties[$name] = $this->{"resolve$name"}();
        }
        return $this->properties[$name];
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $properties = [])
    {
        $this->properties = $properties;
        $this->init();
    }

    protected function init(): void
    {
    }

    public function add(string $property, ...$lines): self
    {
        $this->__get($property);
        foreach ($lines as $line) {
            if (!$line || !fn\hasValue($line, $this->properties[$property])) {
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
        if (!isset(self::$cache[$name])) {
            self::$cache[$name] = new self(array_merge(self::$DEFAULT ?? [], $properties, ['name' => $name]));
        }
        return self::$cache[$name];
    }

    /**
     * @param array $args
     * @return self[]
     */
    public static function all(...$args): array
    {
        return fn\traverse(self::$cache, ...$args);
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
