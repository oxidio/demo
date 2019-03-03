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
     * @param string $name
     * @param array $properties
     *
     * @return static
     */
    public static function get(string $name, array $properties = [])
    {
        static $cache = [];
        if (!isset($cache[static::class][$name])) {
            $cache[static::class][$name] = $obj = new static;
            $properties['name'] = $name;
            $obj->properties    = array_merge(static::$DEFAULT ?? [], $properties);
        }
        return $cache[static::class][$name];
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
