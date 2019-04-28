<?php

use Janisbiz\LightOrm\MySQL\Connection\ConnectionConfig;
use Janisbiz\LightOrm\MySQL\ConnectionPool;
use Janisbiz\LightOrm\MySQL\Generator;
use Janisbiz\LightOrm\MySQL\Generator\GeneratorFactory;

include_once __DIR__ . '/../vendor/autoload.php';

$connectionPool = (new ConnectionPool())
    ->addConnectionConfig(
        new ConnectionConfig(
            'mysql',
            'root',
            'password',
            'light_orm',
            'mysql'
        )
    )
;

(new Generator(new GeneratorFactory(), \sprintf('%s/../var/light-orm', __DIR__)))
    ->generate($connectionPool->getConnection('light_orm'), 'light_orm')
;
