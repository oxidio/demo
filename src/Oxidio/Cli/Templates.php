<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Cli;

use fn\{Cli\IO};
use fn;
use Oxidio\Meta\Template;

class Templates
{
    /**
     * Analyze template structure (files, blocks, includes)
     *
     * @param IO       $io
     * @param string   $basePath [%OX_BASE_PATH% . Application/views/flow/tpl/]
     * @param string   $glob [** / *.tpl]
     */
    public function __invoke(
        IO $io,
        string $basePath = OX_BASE_PATH . 'Application/views/flow/tpl/',
        string $glob     = '**/*.tpl'
    ) {
        fn\traverse(Template::find($basePath . $glob), function(Template $template) use ($io) {
            $io->isVerbose() && $io->title($template->name);
            $io->isVeryVerbose() && $io->listing($template->blocks);
            $io->isVeryVerbose() && $io->listing($template->includes);
        });
    }
}
