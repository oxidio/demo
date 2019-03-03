<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;
use Webmozart\Glob\Glob;

/**
 * @property-read string   $path
 * @property-read string   $name
 * @property-read string[] $blocks
 * @property-read string[] $includes
 */
class Template
{
    use fn\Meta\Properties\ReadOnlyTrait;

    /**
     * @var array
     */
    private $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        return $this->properties[$name] ?? $this->properties[$name] = $this->{"resolve$name"}();
    }

    protected function resolveBlocks(): array
    {
        return $this->tags('block', 'name');
    }

    protected function resolveIncludes(): array
    {
        return $this->tags('include', 'file');
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->path;
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
            yield $name => new static(['path' => $path, 'name' => $name]);
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
