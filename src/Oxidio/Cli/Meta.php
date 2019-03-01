<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Cli;

use fn\Cli\IO;
use Oxidio\Meta\EditionClass;

class Meta
{
    /**
     * Show meta info (tables, fields, templates, blocks)
     *
     * @param IO $io
     */
    public function __invoke(IO $io)
    {
        foreach (EditionClass::all() as $class) {
            $io->title($class->class);
            $io->table(['property', 'value'], [
                ['package', $class->package],
                ['table', $class->table],
                ['(edition) (toString)', "({$class->edition}) ({$class})"],
                ['(namespace) (shortName)', "({$class->namespace}) ({$class->shortName})"],
                ['derivation', $class->derivation->string(' > ')],
                ['parent-edition', $class->parent ? $class->parent->edition : null],
            ]);
        }
    }
}
