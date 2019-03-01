<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;

/**
 * @property-read string   $name
 * @property-read string[] $fields
 */
class Table
{
    use fn\Meta\Properties\ReadOnlyTrait;

    /**
     * @var array
     */
    private $properties;

    public function __construct(string $name, array $fields = [])
    {
        $this->properties = ['name' => $name, 'fields' => $fields];
    }

    public static function cache(string $name, array $fields = []): self
    {
        static $cache = [];
        return $cache[$name] ?? $cache[$name] = new static($name, $fields);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        return $this->properties[$name] ?? $this->properties[$name] = $this->{"resolve$name"}();
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name;
    }
}
