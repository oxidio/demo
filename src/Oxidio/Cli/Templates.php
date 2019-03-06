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
     * @param bool     $filterBlock    Filter templates with blocks
     * @param bool     $filterInclude   Filter templates with includes
     * @param string   $basePath [%OX_BASE_PATH% . Application/views/flow/tpl/]
     * @param string   $glob [** / *.tpl]
     */
    public function __invoke(
        IO $io,
        bool $filterBlock,
        bool $filterInclude,
        string $basePath = OX_BASE_PATH . 'Application/views/flow/tpl/',
        string $glob     = '**/*.tpl'
    ) {
        $keyValue = function(string $value, string $key) {
            return "$key ($value)";
        };

        foreach (Template::find($basePath . $glob) as $template) {
            if ($filterBlock && !$template->blocks) {
                continue;
            }
            if ($filterInclude && !$template->includes) {
                continue;
            }

            $io->isVerbose() && $io->title("{$template->namespace} ({$template->name})");
            $io->isVeryVerbose() && $io->listing(fn\traverse($template->blocks, $keyValue));
            $io->isVeryVerbose() && $io->listing(fn\traverse($template->includes, $keyValue));
        }
    }
}
