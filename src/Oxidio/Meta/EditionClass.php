<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;
use OxidEsales\Facts\Facts;
use OxidEsales\UnifiedNameSpaceGenerator\UnifiedNameSpaceClassMapProvider;
use ReflectionClass;

use OxidEsales\Eshop\{
    Application\Component\Widget\WidgetController,
    Application\Controller\AccountController,
    Application\Controller\Admin\AdminController,
    Application\Controller\Admin\AdminDetailsController,
    Application\Controller\Admin\AdminListController,
    Application\Controller\Admin\DynamicExportBaseController,
    Application\Controller\Admin\ListComponentAjax,
    Application\Controller\Admin\ObjectSeo,
    Application\Controller\Admin\ShopConfiguration,
    Application\Controller\ArticleDetailsController,
    Application\Controller\ArticleListController,
    Application\Controller\FrontendController,
    Core\Base,
    Core\Controller\BaseController,
    Core\Model\BaseModel,
    Core\Model\ListModel,
    Core\Model\MultiLanguageModel,
    Core\SeoEncoder
};

/**
 * @property-read string $class
 * @property-read ReflectionClass $reflection
 * @property-read string $shortName
 * @property-read string $namespace
 * @property-read string $edition
 * @property-read fn\Map|EditionClass[] $derivation
 * @property-read EditionClass $parent = null
 * @property-read string $package
 * @property-read object $instance = null
 * @property-read string $table
 */
class EditionClass
{
    use fn\Meta\Properties\ReadOnlyTrait;

    /**
     * @var string[]
     */
    private const PACKAGES = [
        Base::class                        => '\\',
        SeoEncoder::class                  => '\\Seo',
        BaseModel::class                   => '\\Model',
        MultiLanguageModel::class          => '\\Model\\I18n',
        ListModel::class                   => '\\Model\\List',
        BaseController::class              => '\\Controller',
        FrontendController::class          => '\\Front',
        WidgetController::class            => '\\Front\\Widget',
        AccountController::class           => '\\Front\\Account',
        ArticleListController::class       => '\\Front\\Article\\List',
        ArticleDetailsController::class    => '\\Front\\Article\\Details',
        AdminController::class             => '\\Admin',
        ListComponentAjax::class           => '\\Admin\\Component',
        AdminListController::class         => '\\Admin\\List',
        AdminDetailsController::class      => '\\Admin\\Details',
        DynamicExportBaseController::class => '\\Admin\\Details\\Export',
        ShopConfiguration::class           => '\\Admin\\Details\\Config',
        ObjectSeo::class                   => '\\Admin\\Details\\Seo',
    ];

    /**
     * @var array
     */
    private $properties;

    /**
     * @var array
     */
    private $_edition;

    public function __construct(string $class, array $edition = null)
    {
        if (!$edition) {
            $ns      = explode('\\', $class);
            $edition = [$ns[0]];
            if ($ns[0] === 'OxidEsales') {
                $edition[] = $ns[1];
            }
        }
        $this->_edition = $edition;
        $this->properties = ['class' => $class, 'edition' => implode('\\', $edition) . '\\'];
    }

    private static function cache(string $class, array $edition = null): self
    {
        static $cache = [];
        return $cache[$class] ?? $cache[$class] = new static($class, $edition);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        return $this->properties[$name] ?? $this->properties[$name] = $this->{"_get$name"}();
    }

    protected function _getDerivation(): fn\Map
    {
        $ref = $this->reflection;
        $parents = [];
        while($parent = $ref->getParentClass()) {
            $parents[] = $parent->getName();
            $ref = $parent;
        }
        return fn\map($parents, function($class) {
            return strpos($class, $this->edition) === 0 ? static::cache($class, $this->_edition) : null;
        });
    }

    protected function _getParent(): ?self
    {
        $parent = $this->reflection->getParentClass();
        return $parent ? static::cache($parent->getName()) : null;
    }

    protected function _getReflection(): ReflectionClass
    {
        return new ReflectionClass($this->class);
    }

    protected function _getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    protected function _getNamespace(): string
    {
        return $this->reflection->getNamespaceName();
    }

    protected function _getPackage(): string
    {
        static $packages;
        if ($packages === null) {
            $packages = fn\map(self::PACKAGES)->sort(function(string $left, string $right) {
                return static::cache($left)->derivation->count() - static::cache($right)->derivation->count();
            }, fn\Map\Sort::KEYS | fn\Map\Sort::REVERSE)->traverse;
        }
        foreach ($packages as $baseClass => $package) {
            if (is_a($this->class, $baseClass, true)) {
                return $package;
            }
        }
        return '\\UNKNOWN';
    }

    protected function _getInstance()
    {
        $ref = $this->reflection;
        if ($ref->isInstantiable()) {
            try {
                return $ref->newInstance();
            } catch(\ArgumentCountError $e) {
                return $ref->newInstanceWithoutConstructor();
            }
        }
        return null;
    }

    protected function _getTable()
    {
        return $this->instance instanceof BaseModel ? $this->instance->getCoreTableName() : null;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string) substr($this->class, strlen($this->edition));
    }

    /**
     * @param UnifiedNameSpaceClassMapProvider|null $provider
     *
     * @return fn\Map|self[]
     */
    public static function all(UnifiedNameSpaceClassMapProvider $provider = null): fn\Map
    {
        $provider = $provider ?: new UnifiedNameSpaceClassMapProvider(new Facts);

        return fn\map($provider->getClassMap())->keys(function(string $class) {
            return static::cache($class);
        });
    }
}
