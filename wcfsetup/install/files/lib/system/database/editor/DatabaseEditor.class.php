<?php

namespace wcf\system\database\editor;

use wcf\system\database\Database;
use wcf\system\exception\NotImplementedException;

/**
 * Abstract implementation of a database editor.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @phpstan-type ColumnDefinition array{
 *  autoIncrement?: bool|0|1,
 *  decimals?: int,
 *  default?: string|int|float,
 *  key?: string|false,
 *  length?: ?int,
 *  notNull?: bool|0|1,
 *  type: string,
 *  values?: string,
 * }
 * @phpstan-type ForeignKeyDefinition array{
 *  action?: string,
 *  operation?: string,
 *  columns: string,
 *  referencedTable: string,
 *  referencedColumns: string,
 *  'ON DELETE'?: string,
 *  'ON UPDATE'?: string,
 * }
 * @phpstan-type IndexDefinition array{type: string, columns: string}
 * @phpstan-type ExistingColumnDefinition array{
 *  type: string,
 *  length?: ?int,
 *  notNull: bool,
 *  key: string,
 *  default: string|int|float|null,
 *  autoIncrement: bool,
 *  enumValues: string,
 *  decimals: int,
 * }
 */
abstract class DatabaseEditor
{
    /**
     * database object
     * @var Database
     */
    protected $dbObj;

    /**
     * Creates a new DatabaseEditor object.
     *
     * @param Database $dbObj
     */
    public function __construct(Database $dbObj)
    {
        $this->dbObj = $dbObj;
    }

    /**
     * Returns all existing table names.
     *
     * @return string[] $existingTables
     */
    abstract public function getTableNames();

    /**
     * Returns the columns of a table.
     *
     * @param string $tableName
     * @return mixed[]
     */
    abstract public function getColumns($tableName);

    /**
     * Returns information on the foreign keys of a table.
     *
     * @param string $tableName
     * @return array<string, array{columns: string[], referencedColumns: string[], referencedTable?: string, 'ON DELETE'?: string, 'ON UPDATE'?: string}>
     */
    public function getForeignKeys($tableName)
    {
        throw new NotImplementedException();
    }

    /**
     * Returns the names of the indices of a table.
     *
     * @param string $tableName
     * @return  string[]    $indices
     */
    abstract public function getIndices($tableName);

    /**
     * Returns information on the indices of a table.
     *
     * @param string $tableName
     * @return array<string, array{columns: string[], type: string}>
     */
    public function getIndexInformation($tableName)
    {
        throw new NotImplementedException();
    }

    /**
     * Creates a new database table.
     *
     * @param string $tableName
     * @param array<array{name: string, data: ColumnDefinition}> $columns
     * @param array<array{name: string, data: IndexDefinition|ForeignKeyDefinition}> $indices
     * @return void
     */
    abstract public function createTable($tableName, $columns, $indices = []);

    /**
     * Drops a database table.
     *
     * @param string $tableName
     * @return void
     */
    abstract public function dropTable($tableName);

    /**
     * Adds a new column to an existing database table.
     *
     * @param string $tableName
     * @param string $columnName
     * @param ColumnDefinition $columnData
     * @return void
     */
    abstract public function addColumn($tableName, $columnName, $columnData);

    /**
     * Alters an existing column.
     *
     * @param string $tableName
     * @param string $oldColumnName
     * @param string $newColumnName
     * @param ColumnDefinition $newColumnData
     * @return void
     */
    abstract public function alterColumn($tableName, $oldColumnName, $newColumnName, $newColumnData);

    /**
     * Adds, alters and drops multiple columns at once.
     *
     * @param string $tableName
     * @param array<string|int, mixed[]> $alterData
     * @return void
     */
    public function alterColumns($tableName, $alterData)
    {
        throw new NotImplementedException();
    }

    /**
     * Drops an existing column.
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    abstract public function dropColumn($tableName, $columnName);

    /**
     * Adds a new index to an existing database table.
     *
     * @param string $tableName
     * @param string $indexName
     * @param IndexDefinition $indexData
     * @return void
     */
    abstract public function addIndex($tableName, $indexName, $indexData);

    /**
     * Adds a new foreign key to an existing database table.
     *
     * @param string $tableName
     * @param string $indexName
     * @param ForeignKeyDefinition $indexData
     * @return void
     */
    abstract public function addForeignKey($tableName, $indexName, $indexData);

    /**
     * Drops an existing index.
     *
     * @param string $tableName
     * @param string $indexName
     * @return void
     */
    abstract public function dropIndex($tableName, $indexName);

    /**
     * Drops existing primary keys.
     *
     * @param string $tableName
     * @return void
     */
    abstract public function dropPrimaryKey($tableName);

    /**
     * Drops an existing foreign key.
     *
     * @param string $tableName
     * @param string $indexName
     * @return void
     */
    abstract public function dropForeignKey($tableName, $indexName);
}
