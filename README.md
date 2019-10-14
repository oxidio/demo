# oxidio/project

[![Stable](https://poser.pugx.org/oxidio/project/version)](https://packagist.org/packages/oxidio/project)
[![Downloads](https://poser.pugx.org/oxidio/project/downloads)](https://packagist.org/packages/oxidio/project)
[![Unstable](https://poser.pugx.org/oxidio/project/v/unstable)](https://packagist.org/packages/oxidio/project)
[![License](https://poser.pugx.org/oxidio/project/license)](https://packagist.org/packages/oxidio/project)

## helpful

```shell script

# delete tags
git push origin -d $(git tag -l 'v*')
git tag -d $(git tag -l)
```

## shop

```shell script
git remote add up --no-tags -t master -t b-6.x -t b-6.0.x -t b-6.1.x -f git@github.com:OXID-eSales/oxideshop_ce.git
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
git fetch up 'refs/tags/v*:refs/tags/up/v*'
```

## module-klarna
```shell script
git remote add up --no-tags -t master -f git@github.com:topconcepts/OXID-Klarna-6.git
git fetch up 'refs/tags/v*:refs/tags/up/v*'
```

## facts
```shell script
git remote add up --no-tags -t master -f git@github.com:OXID-eSales/oxideshop-facts.git
git fetch up 'refs/tags/v2*:refs/tags/up/v2*'
git tag -d $(git tag -l 'up/v2*-*')
```

## theme-flow
```shell script
git remote add up --no-tags -t master -t b-2.x -f git@github.com:OXID-eSales/flow_theme.git
git fetch up 'refs/tags/v3*:refs/tags/up/v3*'
```
