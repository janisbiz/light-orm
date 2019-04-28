<?php

namespace Janisbiz\LightOrm\MySQL\Generator;

class GeneratorFactory
{
    /**
     * @param string $databaseName
     * @param \PDO $pdo
     *
     * @return Database
     */
    public function createDatabase($databaseName, \PDO $pdo)
    {

        $tablesInDatabase = $pdo->query('SHOW TABLES', \PDO::FETCH_CLASS, \stdClass::class);

        $tables = [];
        foreach ($tablesInDatabase as $tableInDatabase) {
            $tables[] = $this->createTable($tableInDatabase->{\sprintf('Tables_in_%s', $databaseName)}, $pdo);
        }

        return new Database($databaseName, $tables);
    }

    /**
     * @param string $tableName
     * @param \PDO $pdo
     *
     * @return Table
     */
    private function createTable($tableName, \PDO $pdo)
    {
        $columnsInTable = $pdo->query(
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
    private function createColumn($name, $type, $nullable, $key, $default, $extra)
    {
        return new Column($name, $type, $nullable, $key, $default, $extra);
    }
}
