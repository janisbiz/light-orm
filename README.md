# Light ORM

[![Latest Stable Version](https://poser.pugx.org/janisbiz/light-orm/v/stable)](https://packagist.org/packages/janisbiz/light-orm)
[![codecov](https://codecov.io/gh/janisbiz/light-orm/branch/master/graph/badge.svg?token=Pzt0e1RZLK)](https://codecov.io/gh/janisbiz/light-orm)
[![Build Status](https://travis-ci.com/janisbiz/light-orm.svg?token=FFYBA1tvzN9FThzx1Ca7&branch=master)](https://travis-ci.com/janisbiz/light-orm)
[![Total Downloads](https://poser.pugx.org/janisbiz/light-orm/downloads)](https://packagist.org/packages/janisbiz/light-orm)
![Deps](https://img.shields.io/badge/dependencies-up%20to%20date-brightgreen.svg)

A light ORM for php

## Installing

`composer require janisbiz/light-orm`

## Running tests

There are two ways to run tests:
1) By using docker containers:
    - Copy `.env.dist` to `.env` and adjust defined values for your needs
    - Execute `docker-compose up -d --build`
    - Execute `docker-compose exec php-cli composer install`
    - Execute `docker-compose exec php-cli vendor/bin/phpunit -c phpunit.xml`
2) By using your local php and mysql database environment:
    - Ensure, that your php version is `5.6+`
    - Execute `composer install`
    - Execute `vendor/bin/phpunit -c phpunit.xml`
