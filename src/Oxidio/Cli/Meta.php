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
            $generateModelConstants && $class->table && $this->onModel($class, $tableNs, $fieldNs);
        }

        $io->writeln(fn\traverse($this->renderConstants()));
    }

    private function renderConstants(): \Generator
    {
        if ($this->constants) {
            yield '';
            yield '/** @noinspection SpellCheckingInspection */';
            yield '';
        }

        foreach ($this->constants as $nsName => $ns) {
            yield '/**';
            foreach ($ns->doc as $line) {
                yield " * $line";
            }
            yield ' */';
            yield "namespace $nsName";
            yield '{';
            foreach ($ns->use as $use) {
                yield "    use $use;";
            }
            foreach ($ns->constants as $name => $const) {
                yield '';
                yield '    /**';
                foreach ($const->doc as $line) {
                    yield "     * $line";
                }
                yield '     */';
                yield "    const $name = {$const->value};";
            }
            yield '}';
            yield '';
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

    private static function split(string $name): array
    {
        $last  = strrpos($name, '\\');
        return [substr($name, 0, $last), substr($name, $last + 1)];
    }

    private function nc(string $name): array
    {
        $data = &$this->constants;

        [$ns, $const] = self::split($name);
        if (!isset($data[$ns])) {
            $data[$ns] = (object)['doc' => [], 'use' => [], 'constants' => []];
        }
        if (!isset($data[$ns]->constants[$const])) {
            $data[$ns]->constants[$const] = (object)['value' => 'null', 'doc' => []];
        }
        return [$data[$ns], $data[$ns]->constants[$const]];
    }

    protected function onModel(EditionClass $class, string $tableNs, string $fieldNs): void
    {

        $table = strtoupper($class->table);

        [, $const] = $this->nc("{$tableNs}TABLE\\{$table}");
        $const->value = var_export($class->table->name, true);
        if (!$const->doc) {
            $const->doc = ["{$class->table->comment} [{$class->table->engine}]", '', "@see {$table}\\*"];
        }
        $const->doc[] = "@see \\{$class->name}::__construct";

        foreach ($class->table->columns as $columnConstName => $column) {
            [$ns, $const] = $this->nc("{$tableNs}TABLE\\{$table}\\{$columnConstName}");
            $const->value = var_export($column->name, true);

            $type = $column->type;
            $column->length > 0 && $type .= "({$column->length})";
            $column->default !== null && $type .= " = {$column->default}";

            $const->doc = [$column->comment, '', $type];
            $ns->doc = ["@see \\{$tableNs}TABLE\\{$table}"];

            $fieldConstName = $columnConstName;
            if (strpos($columnConstName, 'OX') === 0) {
                $fieldConstName = substr($columnConstName, 2);
            }
            $fieldConstName = strtoupper($class->shortName) . '_' . $fieldConstName;

            [$ns, $const] = $this->nc("{$fieldNs}{$fieldConstName}");
            $const->value = "TABLE\\{$table}. '__' . TABLE\\{$table}\\{$columnConstName}";
            $const->doc   = [$column->comment];
            $ns->use      = ["{$tableNs}TABLE"];
        }
    }
}
