<?php declare(strict_types=1);

namespace Janisbiz\LightOrm\Dms\MySQL\Generator;

use Janisbiz\LightOrm\Connection\ConnectionInterface;
use Janisbiz\LightOrm\Generator\AbstractDmsFactory;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsColumn;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsDatabase;
use Janisbiz\LightOrm\Dms\MySQL\Generator\Dms\DmsTable;
use Janisbiz\LightOrm\Generator\Dms\DmsColumnInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsDatabaseInterface;
use Janisbiz\LightOrm\Generator\Dms\DmsTableInterface;

class DmsFactory extends AbstractDmsFactory
{
    /**
     * @param string $databaseName
     * @param ConnectionInterface $connection
     *
     * @return DmsDatabase
     */
    public function createDmsDatabase(string $databaseName, ConnectionInterface $connection): DmsDatabaseInterface
    {
        $tablesInDatabase = $connection->query('SHOW TABLES', \PDO::FETCH_CLASS, \stdClass::class);

        $tables = [];
        foreach ($tablesInDatabase as $tableInDatabase) {
            $tables[] = $this->createDmsTable($tableInDatabase->{\sprintf('Tables_in_%s', $databaseName)}, $connection);
        }

        return new DmsDatabase($databaseName, $tables);
    }

    /**
     * @param string $tableName
     * @param ConnectionInterface $connection
     *
     * @return DmsTable
     */
    protected function createDmsTable(string $tableName, ConnectionInterface $connection): DmsTableInterface
    {
        $columnsInTable = $connection->query(
            \sprintf('SHOW COLUMNS FROM %s', $tableName),
            \PDO::FETCH_CLASS,
            \stdClass::class
        );

        $columns = [];
        foreach ($columnsInTable as $columnInTable) {
            $columns[] = $this->createDmsColumn(
                $columnInTable->Field,
                $columnInTable->Type,
                $columnInTable->Null === 'YES',
                $columnInTable->Key,
                (string) $columnInTable->Default,
                $columnInTable->Extra
            );
        }

        return new DmsTable($tableName, $columns);
    }

    /**
     * @param string $name
     * @param string $type
     * @param bool $nullable
     * @param string $key
     * @param string $default
     * @param null|string $extra
     *
     * @return DmsColumn
     */
    protected function createDmsColumn(
        string $name,
        string $type,
        bool $nullable,
        string $key,
        string $default,
        ?string $extra
    ): DmsColumnInterface {
        return new DmsColumn($name, $type, $nullable, $key, $default, $extra);
    }
}
