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
     * @param IO     $io
     * @param bool   $withTable Show only classes with db tables (not abstract models)
     * @param bool   $withTemplate Show only classes with templates (not abstract controllers)
     * @param string $filter Filter pattern
     */
    public function __invoke(IO $io, bool $withTable = false, bool $withTemplate = false, string $filter = null)
    {
        foreach (EditionClass::all() as $class) {
            if ($filter && stripos($class->package, $filter) === false) {
                continue;
            }
            if ($withTable && !$class->table) {
                continue;
            }
            if ($withTemplate && !$class->template) {
                continue;
            }

            $io->title($class->class);
            $io->table(['property', 'value'], [
                ['package', $class->package],
                ['table', $class->table],
                ['template', $class->template],
                ['(edition) (toString)', "({$class->edition}) ({$class})"],
                ['(namespace) (shortName)', "({$class->namespace}) ({$class->shortName})"],
                ['derivation', $class->derivation->string(' > ')],
                ['parent-edition', $class->parent ? $class->parent->edition : null],
            ]);

            if ($fields = $class->fields) {
                $io->listing($fields);
            }
        }
    }
}
