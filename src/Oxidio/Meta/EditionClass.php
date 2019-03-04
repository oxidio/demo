<?php
/**
 * Copyright (C) oxidio. See LICENSE file for license details.
 */

namespace Oxidio\Meta;

use fn;
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
 * @property-read string $name
 * @property-read ReflectionClass $reflection
 * @property-read string $shortName
 * @property-read string $edition
 * @property-read fn\Map|EditionClass[] $derivation
 * @property-read EditionClass $parent = null
 * @property-read string $package
 * @property-read object $instance = null
 * @property-read Table $table = null
 * @property-read string $template
 */
class EditionClass
{
    use ReflectionTrait;

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

    protected function resolveDerivation(): fn\Map
    {
        $ref = $this->reflection;
        $parents = [];
        while($parent = $ref->getParentClass()) {
            $parents[] = $parent->getName();
            $ref = $parent;
        }
        return fn\map($parents, function(string $class) {
            return strpos($class, $this->edition) === 0 ? static::get($class) : null;
        });
    }

    protected function resolveEdition(): string
    {
        $ns      = explode('\\', $this->name);
        $edition = [$ns[0]];
        if ($ns[0] === 'OxidEsales') {
            $edition[] = $ns[1];
        }
        return implode('\\', $edition) . '\\';
    }

    protected function resolveParent(): ?self
    {
        $parent = $this->reflection->getParentClass();
        return $parent ? static::get($parent->getName()) : null;
    }

    protected function resolveReflection(): ReflectionClass
    {
        return new ReflectionClass($this->name);
    }

    protected function resolveShortName(): string
    {
        return $this->reflection->getShortName();
    }

    protected function resolvePackage(): string
    {
        static $packages;
        if ($packages === null) {
            $packages = fn\map(self::PACKAGES)->sort(function(string $left, string $right) {
                return static::get($left)->derivation->count() - static::get($right)->derivation->count();
            }, fn\Map\Sort::KEYS | fn\Map\Sort::REVERSE)->traverse;
        }
        foreach ($packages as $baseClass => $package) {
            if (is_a($this->name, $baseClass, true)) {
                return $package;
            }
        }
        return '\\UNKNOWN';
    }

    protected function resolveInstance()
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

    protected function resolveTable(): ?Table
    {
        if (($model = $this->instance) && $model instanceof BaseModel && $table = $model->getCoreTableName()) {
            return Table::get($table, ['fields' => $model->getFieldNames()]);
        }
        return null;
    }

    protected function resolveTemplate()
    {
        return $this->instance instanceof BaseController ? $this->instance->getTemplateName() : null;
    }
}
