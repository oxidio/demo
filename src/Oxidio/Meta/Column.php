<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;

/**
 * @property-read string $name
 * @property-read string $comment
 * @property-read string $type
 * @property-read bool   $isPrimaryKey
 * @property-read bool   $isAutoIncrement
 * @property-read mixed  $default
 * @property-read int    $length
 */
class Column
{
    use fn\Meta\Properties\ReadOnlyTrait;

    /**
     * @var array
     */
    protected $properties = [
        'name'            => null,
        'comment'         => null,
        'type'            => null,
        'isPrimaryKey'    => false,
        'isAutoIncrement' => false,
        'default'         => null,
        'length'          => null,
    ];

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name;
    }
}
