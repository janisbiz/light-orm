<?php

use Janisbiz\LightOrm\Connection\ConnectionConfig;
use Janisbiz\LightOrm\ConnectionPool;
use Janisbiz\LightOrm\Generator;
use Janisbiz\LightOrm\Dms\MySQL\Generator\DmsFactory;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\BaseEntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\EntityClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\RepositoryClassWriter;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Writer\WriterConfig;

include_once __DIR__ . '/../vendor/autoload.php';

$databaseName = 'light_orm';
$connectionPool = (new ConnectionPool())
    ->addConnectionConfig(
        new ConnectionConfig(
            'mysql',
            'root',
            'password',
            $databaseName,
            'mysql'
        )
    )
;

$directoryPersistent = \implode(
    '',
    [
        __DIR__,
        DIRECTORY_SEPARATOR,
        '..',
        DIRECTORY_SEPARATOR,
        'tests',
        DIRECTORY_SEPARATOR,
        'Behat',
        DIRECTORY_SEPARATOR,
        'Generated'
    ]
);
$directoryNonPersistent = \implode(
    '',
    [
        __DIR__,
        DIRECTORY_SEPARATOR,
        '..',
        DIRECTORY_SEPARATOR,
        'var',
        DIRECTORY_SEPARATOR,
        'light-orm',
        DIRECTORY_SEPARATOR,
        'Generated'
    ]
);

$namespacePersistent = '\JanisBiz\LightOrm\Tests\Behat\Generated\\';
$namespaceNonPersistent = '\JanisBiz\LightOrm\Variable\Generated\\';

$baseEntityClassWriter = new BaseEntityClassWriter(new WriterConfig(
    $directoryNonPersistent,
    $namespaceNonPersistent,
    'Base'
));
$entityClassWriter = new EntityClassWriter(
    new WriterConfig(
        $directoryPersistent,
        $namespacePersistent,
        '',
        'Entity'
    ),
    $baseEntityClassWriter
);
$repositoryClassWriter = new RepositoryClassWriter(
    new WriterConfig(
        $directoryPersistent,
        $namespacePersistent,
        '',
        'Repository'
    ),
    $entityClassWriter
);

(new Generator(new DmsFactory()))
    ->addWriter($baseEntityClassWriter)
    ->addWriter($entityClassWriter)
    ->addWriter($repositoryClassWriter)
    ->generate($connectionPool->getConnection($databaseName), $databaseName)
;
