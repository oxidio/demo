<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use Webmozart\Glob\Glob;
use fn;

/**
 * @property-read string   $path
 * @property-read string   $namespace
 * @property-read string[] $blocks
 * @property-read string[] $includes
 */
class Template
{
    use ReflectionTrait;

    protected function resolveBlocks(): array
    {
        $ns = explode('\\', $this->namespace);
        return fn\traverse($this->tags('block', 'name'), function(string $value) use($ns) {
            $value = str_replace('\\', '_', str_replace($ns, '', $value));
            $value = trim(preg_replace('/__+/', '_', $value), '_');
            return $value  ?  "BLOCK_{$value}" : 'BLOCK';
        });
    }

    protected function resolveIncludes(): array
    {
        return $this->tags('include', 'file');
    }

    protected function resolveNamespace(): string
    {
        return self::unify($this->name);
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

    private static function unify(string $string = null): string
    {
        return str_replace(['.TPL', '/', '_'], ['', '\\', '\\'], strtoupper($string));
    }

    private function tags(string $tag, string $param): array
    {
        $pattern = '/' . sprintf('\[{\s*%s\s+%s\s*=\s*"([^"]+)"\s*}\]', $tag, $param) . '/';
        $content = file_get_contents($this->path);
        $matches = [];
        preg_match_all($pattern, $content, $matches);
        return fn\traverse($matches[1] ?? [], function(string $match) {
            return fn\mapKey($match)->andValue(self::unify($match));
        });
    }
}
