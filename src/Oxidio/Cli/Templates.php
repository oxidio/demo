<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Cli;

use fn\{Cli\IO};
use fn;
use OxidEsales\Eshop\Core\Theme;
use Oxidio\Meta\ReflectionNamespace;
use Oxidio\Meta\Template;

class Templates
{
    /**
     * Analyze template structure (files, blocks, includes)
     *
     * @param IO       $io
     * @param bool     $filterBlock Filter templates with blocks
     * @param bool     $filterInclude Filter templates with includes
     * @param bool     $generate Generate constant namespaces
     * @param string   $basePath [%OX_BASE_PATH% . Application/views/flow/tpl/]
     * @param string   $themeNs Namespace for theme constants [OxidEsales\Eshop\Core\Theme]
     * @param string   $glob [** / *.tpl]
     */
    public function __invoke(
        IO $io,
        bool $filterBlock,
        bool $filterInclude,
        bool $generate,
        string $basePath = OX_BASE_PATH . 'Application/views/flow/tpl/',
        string $themeNs = Theme::class,
        string $glob = '**/*.tpl'
    ) {
        foreach (Template::find($basePath . $glob, ['namespace' => $themeNs]) as $template) {
            if ($filterBlock && !$template->blocks) {
                continue;
            }
            if ($filterInclude && !$template->includes) {
                continue;
            }
            $io->isVerbose() && $this->onVerbose($io, $template);
            $generate && $template->blocks;
        }

        $generate && $io->writeln(['<?php', '']);

        foreach (ReflectionNamespace::all() as $namespace) {
            foreach ($namespace->toPhp() as $line) {
                $io->writeln($line);
            }
            $io->writeln('');
        }
    }

    private function onVerbose(IO $io, Template $template): void
    {
        $keyValue = function(string $value, string $key) {
            return "$key ($value)";
        };

        $io->title("{$template->const->shortName} ({$template->name})");
        $io->isVeryVerbose() && $io->listing(fn\traverse($template->blocks, $keyValue));
        $io->isVeryVerbose() && $io->listing(fn\traverse($template->includes, $keyValue));
    }
}
