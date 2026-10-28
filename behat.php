<?php

use Behat\Config\Config;
use Behat\Config\Formatter\ProgressFormatter;
use Behat\Config\GherkinOptions;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Janisbiz\LightOrm\Tests\Behat\Features\Connection\ConnectionFeatureContext;
use Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Generator\GeneratorFeatureContext;
use Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository\RepositoryCreateFeatureContext;
use Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository\RepositoryDeleteFeatureContext;
use Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository\RepositoryFeatureContext;
use Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository\RepositoryReadFeatureContext;
use Janisbiz\LightOrm\Tests\Behat\Features\Dms\MySQL\Repository\RepositoryUpdateFeatureContext;

return (new Config())
    ->withProfile((new Profile('default', [
        'exceptions' => [
            'verbosity' => 2,
        ],
    ]))
        ->withFormatter(new ProgressFormatter())
        ->withGherkinOptions((new GherkinOptions())
            ->withCacheDir('%paths.base%/var/behat/cache'))
        ->withSuite((new Suite('connection'))
            ->withContexts(ConnectionFeatureContext::class)
            ->withPaths('%paths.base%/tests/Behat/Features/Connection'))
        ->withSuite((new Suite('dms-mysql-generator'))
            ->withContexts(
                ConnectionFeatureContext::class,
                GeneratorFeatureContext::class
            )
            ->withPaths('%paths.base%/tests/Behat/Features/Dms/MySQL/Generator'))
        ->withSuite((new Suite('dms-mysql-repository'))
            ->withContexts(
                ConnectionFeatureContext::class,
                RepositoryFeatureContext::class,
                RepositoryCreateFeatureContext::class,
                RepositoryReadFeatureContext::class,
                RepositoryUpdateFeatureContext::class,
                RepositoryDeleteFeatureContext::class
            )
            ->withPaths('%paths.base%/tests/Behat/Features/Dms/MySQL/Repository')));
