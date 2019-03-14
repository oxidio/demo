<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Cli;

use fn\{Cli\IO};
use fn;
use OxidEsales\Facts\Facts;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use Oxidio\Meta\{Column, EditionClass, ReflectionNamespace};

class Meta
{
    /**
     * Show meta info (tables, fields, templates, blocks)
     *
     * @param IO       $io
     * @param bool     $filterTable    Filter classes with db tables (not abstract models)
     * @param bool     $filterTemplate Filter classes with templates (not abstract controllers)
     * @param string   $filter         Filter classes by pattern
     * @param string   $tableNs        Namespace for table constants [OxidEsales\Eshop\Core\Database\TABLE]
     * @param string   $fieldNs        Namespace for field constants [OxidEsales\Eshop\Core\Field]
     * @param string[] $action         (model-const)
     */
    public function __invoke(
        IO $io,
        bool $filterTable = false,
        bool $filterTemplate = false,
        string $filter = null,
        string $tableNs = 'OxidEsales\\Eshop\\Core\\Database\\TABLE',
        string $fieldNs = 'OxidEsales\\Eshop\\Core\\Field',
        ...$action
    ) {
        $generateModelConstants = fn\hasValue('model-const', $action);

        foreach (fn\keys((new UnifiedNameSpaceClassMapProvider(new Facts))->getClassMap()) as $name) {

            $class = EditionClass::get($name, ['tableNs' => $tableNs, 'fieldNs' => $fieldNs]);

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
            $generateModelConstants && $class->table && fn\traverse($class->table->columns, function(Column $column) {
                $column->const;
            });
        }

        $generateModelConstants && $io->writeln(['<?php', '']);

        foreach (ReflectionNamespace::all() as $namespace) {
            foreach ($namespace->toPhp() as $line) {
                $io->writeln($line);
            }
            $io->writeln('');
        }
    }

    private function onVerbose(IO $io, EditionClass $class): void
    {
        $io->title($class->name);
        $io->table(['property', 'value'], [
            ['package', $class->package],
            ['table', $table = $class->table],
            ['template', $class->template],
        ]);

        if ($table && $io->isVeryVerbose()) {
            $io->listing($class->fields);
            $table->comment;
        }
    }
}
