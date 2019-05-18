# Light ORM

[![Latest Stable Version](https://poser.pugx.org/janisbiz/light-orm/v/stable)](https://packagist.org/packages/janisbiz/light-orm)
[![codecov](https://codecov.io/gh/janisbiz/light-orm/branch/master/graph/badge.svg?token=Pzt0e1RZLK)](https://codecov.io/gh/janisbiz/light-orm)
[![Build Status](https://travis-ci.com/janisbiz/light-orm.svg?token=FFYBA1tvzN9FThzx1Ca7&branch=master)](https://travis-ci.com/janisbiz/light-orm)
[![Total Downloads](https://poser.pugx.org/janisbiz/light-orm/downloads)](https://packagist.org/packages/janisbiz/light-orm)
![Deps](https://img.shields.io/badge/dependencies-up%20to%20date-brightgreen.svg)

A light ORM for php

## Installing

`composer require janisbiz/light-orm`

## About

light-orm has been used for a while in some of my companies projects. As it was growing, I decided to open-source it for 
the rest of community. It has good unit test coverage and a great support for integration testing together with desired
DMS using Behat.

Currently these DMS are supported:
 - MySQL
 
There is still a lot work to do, so it would be great to have some contributors for future improvements

## Examples

How to use generator, please see [generate.php](/8d7d641559e04a14be346ab26e6dd3e89206e83c/bin/generate.php)
How to use repositories, please see examples of Repositories ans Entities on [Behat test suites](/8d7d641559e04a14be346ab26e6dd3e89206e83c/tests/Behat/Bootstrap/Generated/LightOrmMysql) 

## TODO

1) Add logger for logging queries executed against DMS
2) Add support for other DMS than MySQL (preferably - Postgres)
3) Add migration support

## Running tests

There are two ways to run tests:
1) By using docker containers:
    - Copy `.env.dist` to `.env` and adjust defined values for your needs
    - Execute `make start_dev`
    - Execute `make test`
2) By using your local php and mysql database environment:
    - Ensure, that your php version is `5.6+`
    - Execute `composer install`
    - Execute `vendor/bin/phpcs -p ./src -p ./tests --standard=PHPCompatibility,PSR2 --runtime-set testVersion 5.6-; vendor/bin/phpunit -c phpunit.xml; vendor/bin/behat;`
