<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

/**
 * @property-read string $comment
 * @property-read string $type
 * @property-read bool   $isPrimaryKey
 * @property-read bool   $isAutoIncrement
 * @property-read mixed  $default
 * @property-read int    $length
 */
class Column
{
    use ReflectionTrait;

    protected static $DEFAULT = [
        'comment'         => null,
        'type'            => null,
        'isPrimaryKey'    => false,
        'isAutoIncrement' => false,
        'length'          => null,
        'default'         => null,
    ];
}
