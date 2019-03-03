<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use Webmozart\Glob\Glob;

/**
 * @property-read string   $path
 * @property-read string[] $blocks
 * @property-read string[] $includes
 */
class Template
{
    use ReflectionTrait;

    protected function resolveBlocks(): array
    {
        return $this->tags('block', 'name');
    }

    protected function resolveIncludes(): array
    {
        return $this->tags('include', 'file');
    }

    /**
     * @param string $glob
     *
     * @return \Generator|self[]
     */
    public static function find(string $glob): \Generator
    {
        $basePath = Glob::getBasePath($glob);
        $offset   = strlen($basePath) + 1;
        foreach (Glob::glob($glob) as $path) {
            $name = substr($path, $offset);
            yield $name => static::get($name, ['path' => $path]);
        }
    }

    private function tags(string $tag, string $param): array
    {
        $pattern = '/' . sprintf('\[{\s*%s\s+%s\s*=\s*"([^"]+)"\s*}\]', $tag, $param) . '/';
        $content = file_get_contents($this->path);
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        return $matches[1] ?? [];
    }
}
