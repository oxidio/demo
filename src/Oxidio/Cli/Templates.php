<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Cli;

use fn\{Cli\IO};

class Templates
{
    /**
     * Analyze template structure (files, blocks, includes)
     *
     * @param IO       $io
     * @param string   $from [%OX_BASE_PATH% . Application/views/flow/]
     * @param string[] $action
     */
    public function __invoke(
        IO $io,
        string $from = OX_BASE_PATH . 'Application/views/flow/',
        ...$action
    ) {
        $io->error('@todo implement it: ' . $from);
    }
}
