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
        $last = strrpos($name = $this->name, '\\');
        $this->properties['shortName'] = substr($name, $last + 1);

        $ns = $this->properties['namespace'] = ReflectionNamespace::get(substr($name, 0, $last));
        $ns->add('constants', $this);
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
