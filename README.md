# oxidio/project

[![Stable](https://poser.pugx.org/oxidio/project/version)](https://packagist.org/packages/oxidio/project)
[![Downloads](https://poser.pugx.org/oxidio/project/downloads)](https://packagist.org/packages/oxidio/project)
[![Unstable](https://poser.pugx.org/oxidio/project/v/unstable)](https://packagist.org/packages/oxidio/project)
[![License](https://poser.pugx.org/oxidio/project/license)](https://packagist.org/packages/oxidio/project)

## version matrix

| - | master | 6.2 | 6.1 | 6.0 | 4.10 |
|---|---|---|---|---|---|
| [esales:meta](https://packagist.org/packages/oxid-esales/oxideshop-metapackage-ce) | * | * <sup>v6.2.0-rc.2</sup> | * <sup>v6.1.5</sup> | master <sup>v6.0.6</sup> | - |
| [esales:shop](https://packagist.org/packages/oxid-esales/oxideshop-ce) | master | b-6.2.x <sup>v6.5.2</sup> | b-6.1.x <sup>v6.3.7</sup> | b-6.0.x <sup>v6.2.4</sup> | b-5.3-ce <sup>v4.10.8</sup> |
| [oxidio:shop](https://packagist.org/packages/oxidio/shop) | oxidio-master | oxidio-6.2.x | oxidio-6.1.x | - | - |
| [esales:flow](https://packagist.org/packages/oxid-esales/flow-theme) | master | * <sup>v3.4.1</sup> | * <sup>v3.3.0</sup> | b-3.x <sup>v3.0.0</sup> | ? |
| [oxidio:flow](https://packagist.org/packages/oxidio/theme-flow) | oxidio-master | * | oxidio-3.x | - | - |
| [esales:generator](https://packagist.org/packages/oxid-esales/oxideshop-unified-namespace-generator) | * | * | * <sup>v2.0.1</sup> | b-1.x <sup>v1.0.0</sup> | ? |
| [oxidio:generator](https://packagist.org/packages/oxidio/unified-namespace-generator) | * | * | master | - | - |
| [esales:composer](https://packagist.org/packages/oxid-esales/oxideshop-composer-plugin) | * | b-6.x <sup>v5.1.0</sup> | * <sup>v2.0.4</sup> | b-2.x <sup>v2.0.3</sup> | ? |
| [oxidio:composer](https://packagist.org/packages/oxidio/composer-plugin) | oxidio-master | oxidio-6.x | oxidio-2.x | - | - |
| [esales:facts](https://packagist.org/packages/oxid-esales/oxideshop-facts) | master | * <sup>v2.3.2</sup> | * | b-1.x <sup>v2.3.1</sup> | ? |
| [oxidio:facts](https://packagist.org/packages/oxidio/facts) | oxidio-master | * | oxidio-1.x | - | - |
| [esales:migration](https://packagist.org/packages/oxid-esales/oxideshop-doctrine-migration-wrapper) | * | * | * | master <sup>v2.1.3</sup> | ? |
| [oxidio:migration](https://packagist.org/packages/oxidio/doctrine-migration-wrapper) | * | * | master | - | - |
| [esales:paypal](https://packagist.org/packages/oxid-esales/paypal-module) | master | b-6.x <sup>v6.1.0</sup> | * <sup>v5.3.1</sup> | b-5.x <sup>v5.2.2</sup> | - |
| [oxidio:paypal](https://packagist.org/packages/oxidio/module-paypal) | oxidio-master | oxidio-6.x | * | oxidio-5.x | - |
| [topconsepts:klarna](https://packagist.org/packages/topconcepts/oxid-klarna-6) | * | * <sup>v5.1.3</sup> | * <sup>v4.3.0</sup> | master <sup>v4.0.1</sup> | - |
| [oxidio:klarna](https://packagist.org/packages/oxidio/module-klarna) | * | * | * | oxidio-master | - |
| [oxidio:oxidio](https://packagist.org/packages/oxidio/oxidio) | * | master | oxidio-6.1.x | - | - |


## helpful

```shell script

# delete tags
git push origin -d $(git tag -l 'v*')
git tag -d $(git tag -l 'v*')
```

## shop

```shell script
git remote add up --no-tags -t master -t b-6.x -t b-6.2.x -t b-6.1.x -t b-6.0.x -f git@github.com:OXID-eSales/oxideshop_ce.git
git fetch up 'refs/tags/v6*:refs/tags/up/v6*'
git tag -d $(git tag -l 'up/v6*-*')

# list remote
git ls-remote -h up master b-6*x # branches
git ls-remote -t up 'v6.[+(0-9)].[+(0-9)]'  # tags

# list local
git tag --list up/*
git branch --list up/*

# delete local
git tag -d $(git tag -l up/*)
git branch -D $(git branch --list up/*)
```

## oxidio
```shell script
git remote add origin -f git@github.com:oxidio/oxidio.git
```

## oxidio/composer-plugin
```shell script
git remote add up --no-tags -t master -t b-2.x -f git@github.com:OXID-eSales/oxideshop_composer_plugin.git
git remote set-branches --add up b-6.x
git fetch up 'refs/tags/v*:refs/tags/up/v*'
```

## oxidio/unified-namespace-generator
```shell script
git remote add up --no-tags -t master -t b-1.x -f git@github.com:OXID-eSales/oxideshop-unified-namespace-generator.git
```

## module-klarna
```shell script
git remote add up --no-tags -t master -f git@github.com:topconcepts/OXID-Klarna-6.git
git fetch up 'refs/tags/v*:refs/tags/up/v*'
```

## facts
```shell script
git remote add up --no-tags -t master -t b-1.x -f git@github.com:OXID-eSales/oxideshop-facts.git
git fetch up 'refs/tags/v2*:refs/tags/up/v2*'
git tag -d $(git tag -l 'up/v2*-*')
```

## theme-flow
```shell script
git remote add up --no-tags -t master -t b-2.x -t b-3.x -t b-4.x -f git@github.com:OXID-eSales/flow_theme.git
git fetch up 'refs/tags/v3*:refs/tags/up/v3*'
```
