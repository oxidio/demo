<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use Generator;
use Reflector;
use fn;

/**
 * @property-read ReflectionNamespace $namespace
 * @property-read string[]            $docBlock
 * @property-read string              $shortName
 * @property-read string              $value
 */
class ReflectionConstant implements Reflector
{
    use ReflectionTrait;

    protected const DEFAULT = ['docBlock' => [], 'value' => null, 'name' => null];

    protected function init(): void
    {
        $this->name;
    }

    public function setValue($value, $export = false): self
    {
        $this->resolved['value'] = $export ? var_export($value, true) : $value;
        return $this;
    }

    protected function resolveName($name): string
    {
        $last = strrpos($name, '\\');
        $last = substr($name, 0, $last);
        $this->resolved['namespace'] = ReflectionNamespace::get($last)->add('constants', $this);
        $isReserved = fn\hasValue(strtolower($this->namespace->relative($name)), fn\Composer\DIPackages::RESERVED);
        return $isReserved ? $name . '_' : $name;
    }

    protected function resolveShortName(): string
    {
        return $this->namespace->relative($this->name);
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
