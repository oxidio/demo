<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Cli;

use fn\{Cli\IO};
use fn;
use OxidEsales\Facts\Facts;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use Oxidio\Meta\{EditionClass, ReflectionConstant, ReflectionNamespace};

class Meta
{
    /**
     * Show meta info (tables, fields, templates, blocks)
     *
     * @param IO       $io
     * @param bool     $filterTable    Filter classes with db tables (not abstract models)
     * @param bool     $filterTemplate Filter classes with templates (not abstract controllers)
     * @param string   $filter         Filter classes by pattern
     * @param string   $tableNs        Namespace for TABLE\* constants [OxidEsales\Eshop\Core\Database\]
     * @param string   $fieldNs        Namespace for field constants [OxidEsales\Eshop\Core\Field\]
     * @param string[] $action         (model-const)
     */
    public function __invoke(
        IO $io,
        bool $filterTable = false,
        bool $filterTemplate = false,
        string $filter = null,
        string $tableNs = 'OxidEsales\\Eshop\\Core\\Database\\',
        string $fieldNs = 'OxidEsales\\Eshop\\Core\\Field\\',
        ...$action
    ) {
        $generateModelConstants = fn\hasValue('model-const', $action);

        foreach (fn\keys((new UnifiedNameSpaceClassMapProvider(new Facts))->getClassMap()) as $name) {
            $class = EditionClass::get($name);
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
            $generateModelConstants && $class->table && $this->onModel($class, $tableNs, $fieldNs);
        }

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
            $io->listing($table->fields);
            $table->comment;
        }
    }

    protected function onModel(EditionClass $class, string $tableNs, string $fieldNs): void
    {
        $table = strtoupper($class->table);
        ReflectionConstant::get("{$tableNs}TABLE\\{$table}", [
            'value'    => var_export($class->table->name, true),
            'docBlock' => [
                "{$class->table->comment} [{$class->table->engine}]",
                '',
                "@see {$table}\\*"
            ]
        ])->add('docBlock', "@see \\{$class->name}::__construct");

        foreach ($class->table->columns as $columnConstName => $column) {
            $type = $column->type;
            $column->length > 0 && $type .= "({$column->length})";
            $column->default !== null && $type .= " = {$column->default}";

            ReflectionConstant::get("{$tableNs}TABLE\\{$table}\\{$columnConstName}", [
                'value'    => var_export($column->name, true),
                'docBlock' => [$column->comment, '', $type]]
            )->namespace->add('docBlock',"@see \\{$tableNs}TABLE\\{$table}");


            $fieldConstName = $columnConstName;
            if (strpos($columnConstName, 'OX') === 0) {
                $fieldConstName = substr($columnConstName, 2);
            }
            $fieldConstName = strtoupper($class->shortName) . '_' . $fieldConstName;

            ReflectionConstant::get("{$fieldNs}{$fieldConstName}", [
                'value'    => "TABLE\\{$table}. '__' . TABLE\\{$table}\\{$columnConstName}",
                'docBlock' => [$column->comment]
            ])->namespace->add('use',"{$tableNs}TABLE");
        }
    }
}
