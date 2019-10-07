# oxidio/project

[![Stable](https://poser.pugx.org/oxidio/project/version)](https://packagist.org/packages/oxidio/project)
[![Downloads](https://poser.pugx.org/oxidio/project/downloads)](https://packagist.org/packages/oxidio/project)
[![Unstable](https://poser.pugx.org/oxidio/project/v/unstable)](https://packagist.org/packages/oxidio/project)
[![License](https://poser.pugx.org/oxidio/project/license)](https://packagist.org/packages/oxidio/project)


## oxidio/shop

```shell script

# create remote
git remote add up --no-tags -t master -t b-6.x -t b-6.0.x -t b-6.1.x -f git@github.com:OXID-eSales/oxideshop_ce.git
# create from remote
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
