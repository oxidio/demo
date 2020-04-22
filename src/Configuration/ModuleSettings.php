<?php declare(strict_types=1);
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Project\Configuration;

use IteratorAggregate;

class ModuleSettings implements IteratorAggregate
{
    public function getIterator()
    {
        yield 'oxidio/module-bar' => [
            'string' => __CLASS__
        ];
    }
}
