<?php

namespace Janisbiz\LightOrm\Dms\MySQL\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\AbstractGeneratorFactory;
use Janisbiz\LightOrm\Generator\Column;
use Janisbiz\LightOrm\Generator\Database;
use Janisbiz\LightOrm\Generator\Table;

class GeneratorFactory extends AbstractGeneratorFactory
{
    /**
     * @param string $databaseName
     * @param ConnectionInterface $connection
     *
     * @return Database
     */
    public function createDatabase($databaseName, ConnectionInterface $connection)
    {

        $tablesInDatabase = $connection->query('SHOW TABLES', \PDO::FETCH_CLASS, \stdClass::class);

        $tables = [];
        foreach ($tablesInDatabase as $tableInDatabase) {
            $tables[] = $this->createTable($tableInDatabase->{\sprintf('Tables_in_%s', $databaseName)}, $connection);
        }

        return new Database($databaseName, $tables);
    }

    /**
     * @param string $tableName
     * @param ConnectionInterface $connection
     *
     * @return Table
     */
    protected function createTable($tableName, ConnectionInterface $connection)
    {
        $columnsInTable = $connection->query(
            \sprintf('SHOW COLUMNS FROM %s', $tableName),
            \PDO::FETCH_CLASS,
            \stdClass::class
        );

        $columns = [];
        foreach ($columnsInTable as $columnInTable) {
            $columns[] = $this->createColumn(
                $columnInTable->Field,
                $columnInTable->Type,
                $columnInTable->Null === 'YES',
                $columnInTable->Key,
                $columnInTable->Default,
                $columnInTable->Extra
            );
        }

        return new Table($tableName, $columns);
    }

    /**
     * @param string $name
     * @param string $type
     * @param bool $nullable
     * @param string $key
     * @param string $default
     * @param null|string $extra
     *
     * @return Column
     */
    protected function createColumn($name, $type, $nullable, $key, $default, $extra)
    {
        return new Column($name, $type, $nullable, $key, $default, $extra);
    }
}
