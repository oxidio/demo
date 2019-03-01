<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Cli;

use fn\{Cli\IO};
use fn;
use Oxidio\Meta\EditionClass;

class Meta
{
    protected $constants = [];

    /**
     * Show meta info (tables, fields, templates, blocks)
     *
     * @param IO       $io
     * @param bool     $filterTable    Filter classes with db tables (not abstract models)
     * @param bool     $filterTemplate Filter classes with templates (not abstract controllers)
     * @param string   $filter         Filter classes by pattern
     * @param string[] $action         (tables)
     */
    public function __invoke(
        IO $io,
        bool $filterTable = false,
        bool $filterTemplate = false,
        string $filter = null,
        array $action = []
    ) {
        $onTable = fn\hasValue('tables', $action);

        foreach (EditionClass::all() as $class) {
            if ($filter && stripos($class->package, $filter) === false) {
                continue;
            }
            if ($filterTable && !$class->table) {
                continue;
            }
            if ($filterTemplate && !$class->template) {
                continue;
            }
            $io->isVerbose() && $this->onVerbose($io, $class);
            $onTable && $class->table && $this->onTable($class);
        }

        $io->writeln(fn\traverse($this->renderConstants()));
    }

    private function renderConstants(): \Generator
    {
        yield null;
        foreach ($this->constants as $ns => $constants) {
            yield "namespace $ns";
            yield '{';
            foreach ($constants as $constant => $value) {
                yield "    const $constant = '$value';";
            }
            yield '}';
            yield null;
        }
    }

    private function onVerbose(IO $io, EditionClass $class): void
    {
        $io->title($class->class);
        $io->table(['property', 'value'], [
            ['package', $class->package],
            ['table', $table = $class->table],
            ['template', $class->template],
            ['(edition) (toString)', "({$class->edition}) ({$class})"],
            ['(namespace) (shortName)', "({$class->namespace}) ({$class->shortName})"],
            ['derivation', $class->derivation->string(' > ')],
            ['parent-edition', $class->parent ? $class->parent->edition : null],
        ]);

        $io->isVeryVerbose() && $table && $io->listing($table->fields);
    }

    protected function onTable(EditionClass $class): void
    {
        $this->constants['TABLE'][$table = strtoupper($class->table)] = $class->table;
        foreach ($class->fields as $field) {
            $this->constants["TABLE\\{$table}"][strtoupper($field)] = $field;
        }

//        oxNew(Wrapping::class)->getFieldData(Wrapping\ID);
//        oxNew(Wrapping::class)->assign([Wrapping\ID => 'value']);
    }
}
