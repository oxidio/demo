<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use Generator;
use Reflector;
use fn;

/**
 * @property-read ReflectionConstant[] $constants
 * @property-read string               $shortName
 * @property-read string[]             $docBlock
 * @property-read string[]             $use
 */
class ReflectionNamespace implements Reflector
{
    use ReflectionTrait;

    protected static $DEFAULT = ['constants' => [], 'docBlock' => [], 'use' => []];

    public function relative($fqn): string
    {
        return strrpos($fqn, $this->name) === 0 ? substr($fqn, strlen($this->name)) : $fqn;
    }

    protected function resolveName(string $name = null): string
    {
        return substr($name, -1) === '\\' ? $name : $name . '\\';
    }

    protected function resolveShortName(): string
    {
        $parts = array_filter(explode('\\', $this->name));
        return end($parts) . '\\';
    }

    /**
     * @param array $args
     * @return self[]
     */
    public static function all(...$args): iterable
    {
        return fn\map(self::$cache, ...$args)->sort(function(self $left, self $right) {
            return (count($left->use) - count($right->use)) ?: strcmp($left, $right);
        });
    }

    public function toPhp(): Generator
    {
        yield '/**';
        foreach ($this->docBlock as $line) {
            yield " * $line";
        }
        yield ' */';
        yield 'namespace ' . substr($this->name, 0, -1);
        yield '{';
        foreach ($this->use as $use) {
            yield "    use $use;";
        }
        foreach ($this->constants as $const) {
            yield '';
            yield from $const->toPhp();
        }
        yield '}';
    }
}
