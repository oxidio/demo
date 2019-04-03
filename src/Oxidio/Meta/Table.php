<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use Doctrine\DBAL\Schema\Table as SchemaTable;
use fn;
use Oxidio;

/**
 * @property-read EditionClass       $class
 * @property-read ReflectionConstant $const
 * @property-read Column[]           $columns
 * @property-read string             $comment
 * @property-read string             $engine
 */
class Table
{
    use ReflectionTrait;

    protected function init(): void
    {
        $this->const;
        $this->columns;
    }

    private function detail($detail): ?string
    {
        static $details;
        $details || $details = fn\traverse(Oxidio\db()->tables, function(SchemaTable $table) {
            return fn\mapKey($table->getName())->andValue($table->getOptions());
        });
        return $details[$this->name][$detail] ?? null;
    }

    public function resolveConst(): ReflectionConstant
    {
        $table = strtoupper($this->name);
        return ReflectionConstant::get("{$this->class->tableNs}{$table}", [
            'value'    => "'{$this->name}'",
            'docBlock' => [
                "{$this->comment} [{$this->engine}]",
                '',
                "@see {$table}\\*"
            ]
        ]);
    }

    protected function resolveComment(): ?string
    {
        return $this->detail('comment');
    }

    protected function resolveEngine(): ?string
    {
        return $this->detail('engine');
    }

    protected function resolveColumns(): array
    {
        $columns = [];
        $nls     = [];
        foreach (Oxidio\db()->metaColumns($this->name) as $column) {
            $name = strtolower($column->name);
            if (is_numeric(substr($name, ($last = strrpos($name, '_')) + 1))) {
                $nls[substr($name, 0, $last)] = true;
                continue;
            }
            $columns[$name] = [
                'table'           => $this,
                'name'            => $name,
                'type'            => $column->type,
                'comment'         => $column->comment,
                'isPrimaryKey'    => $column->primary_key,
                'isAutoIncrement' => $column->auto_increment,
                'length'          => $column->max_length,
                'default'         => $column->has_default ? $column->default_value : null,
            ];
        }

        return fn\traverse($columns, static function(array $column, $name) use($nls) {
            $column['type'] .= (($nls[$name] ?? false) ? '-i18n' : '');
            return Column::create($name, $column);
        });
    }
}
