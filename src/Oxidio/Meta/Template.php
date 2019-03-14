<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use Generator;
use Webmozart\Glob\Glob;
use fn;

/**
 * @property-read ReflectionNamespace $namespace
 * @property-read ReflectionConstant  $const
 * @property-read Template[]          $includes
 * @property-read string              $path
 * @property-read string[]            $blocks
 */
class Template
{
    use ReflectionTrait;

    protected static $DEFAULT = ['namespace' => null, 'path' => null];

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
        return fn\traverse($this->tags('include', 'file'), function(string $name) {
            return static::get($name);
        });
    }

    protected function resolveConst(): ReflectionConstant
    {
        $docBlock = fn\traverse($this->includes, function(Template $template) {
            return "@see $template";
        });

        $const = ReflectionConstant::get($this->namespace . self::unify($this->name), [
            'value'    => var_export($this->name, true),
            'docBlock' => $docBlock
        ]);

        foreach ($this->blocks as $value => $block) {
            ReflectionConstant::get("$const\\{$block}", [
                'value' => var_export($value, true),
            ]);
        }

        return $const;
    }

    protected function resolveNamespace($namespace = null): ReflectionNamespace
    {
        return ReflectionNamespace::get((string)$namespace);
    }

    /**
     * @param string $glob
     *
     * @param array $properties
     * @return Generator|self[]
     */
    public static function find(string $glob, array $properties = []): Generator
    {
        $basePath = Glob::getBasePath($glob);
        $offset   = strlen($basePath) + 1;
        foreach (Glob::glob($glob) as $path) {
            $name = substr($path, $offset);
            yield $name => static::get($name, ['path' => $path] + $properties);
        }
    }

    private static function unify(string $string = null): string
    {
        return str_replace(['.TPL', '/', '_'], ['', '_', '_'], strtoupper($string));
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
