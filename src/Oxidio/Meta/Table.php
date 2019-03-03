<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;

/**
 * @property-read Column[] $columns
 * @property-read string   $comment
 * @property-read string   $engine
 * @property-read string[] $fields
 */
class Table
{
    use ReflectionTrait;

    private function detail($detail): ?string
    {
        static $details;
        if ($details === null) {
            $select = DatabaseProvider::getDb()->select('
                SELECT TABLE_NAME, ENGINE, TABLE_COMMENT 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = ?', [
                    Registry::getConfig()->getConfigParam('dbName'),
                    'BASE TABLE'
            ]);

            $details = fn\traverse($select, function(array $row) {
                return fn\mapKey($row[0])->andValue(['engine' => $row[1], 'comment' => $row[2]]);
            });
        }
        return $details[$this->name][$detail] ?? null;
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
        foreach (DatabaseProvider::getDb()->metaColumns($this->name) as $column) {
            $name = strtolower($column->name);
            if (is_numeric(substr($name, ($last = strrpos($name, '_')) + 1))) {
                $nls[substr($name, 0, $last)] = true;
                continue;
            }
            $columns[$name] = [
                'name'            => $name,
                'type'            => $column->type,
                'comment'         => $column->comment,
                'isPrimaryKey'    => $column->primary_key,
                'isAutoIncrement' => $column->auto_increment,
                'length'          => $column->max_length,
                'default'         => $column->has_default ? $column->default_value : null,
            ];
        }

        return fn\traverse($this->fields, function(string $field, &$key) use($columns, $nls) {
            $key = strtoupper($field);
            if ($column = $columns[$field] ?? null) {
                $column['type'] .= (($nls[$field] ?? false) ? '-i18n' : '');
                return Column::get($field, $column);
            }
            return Column::get($field);
        });
    }
}
