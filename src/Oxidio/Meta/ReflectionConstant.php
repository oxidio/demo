<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use Generator;
use Reflector;

/**
 * @property-read ReflectionNamespace $namespace
 * @property-read string[]            $docBlock
 * @property-read string              $shortName
 * @property-read string              $value
 */
class ReflectionConstant implements Reflector
{
    use ReflectionTrait;

    protected static $DEFAULT = ['docBlock' => []];

    protected function init(): void
    {
        $this->namespace; // register namespace
    }

    protected function resolveNamespace(): ReflectionNamespace
    {
        $last = strrpos($name = $this->name, '\\');
        return ReflectionNamespace::get(substr($name, 0, $last))->add('constants', $this);
    }

    protected function resolveShortName(): string
    {
        return $this->namespace->relative($this);
    }

    public function toPhp(): Generator
    {
        yield '    /**';
        foreach ($this->docBlock as $line) {
            yield "     * $line";
        }
        yield '     */';
        yield "    const {$this->shortName} = {$this->value};";
    }
}
