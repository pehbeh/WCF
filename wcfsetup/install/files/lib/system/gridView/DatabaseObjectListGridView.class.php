<?php

namespace wcf\system\gridView;

use LogicException;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;

/**
 * Abstract implementation of a grid view that uses a database object list as the data source.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class DatabaseObjectListGridView extends AbstractGridView
{
    protected DatabaseObjectList $objectList;
    private int $objectCount;
    public int $counter = 0;

    #[\Override]
    public function getRows(): array
    {
        $this->getObjectList()->readObjects();

        $this->counter++;

        return $this->getObjectList()->getObjects();
    }

    #[\Override]
    public function countRows(): int
    {
        if (!isset($this->objectCount)) {
            $this->objectCount = $this->getObjectList()->countObjects();
        }

        return $this->objectCount;
    }

    #[\Override]
    protected function getData(mixed $row, string $identifer): mixed
    {
        \assert($row instanceof DatabaseObject);

        return $row->__get($identifer);
    }

    /**
     * Initializes the database object list.
     */
    protected function initObjectList(): void
    {
        $this->objectList = $this->createObjectList();
        $this->objectList->sqlLimit = $this->getRowsPerPage();
        $this->objectList->sqlOffset = ($this->getPageNo() - 1) * $this->getRowsPerPage();
        if ($this->getSortField()) {
            $column = $this->getColumn($this->getSortField());
            if ($column && $column->getSortByDatabaseColumn()) {
                $this->objectList->sqlOrderBy = $column->getSortByDatabaseColumn() . ' ' . $this->getSortOrder();
            } else {
                $this->objectList->sqlOrderBy = $this->objectList->getDatabaseTableAlias() .
                    '.' . $this->getSortField() . ' ' . $this->getSortOrder();
            }

            $this->objectList->sqlOrderBy .= ',' . $this->objectList->getDatabaseTableAlias() .
                '.' . $this->objectList->getDatabaseTableIndexName() . ' ' . $this->getSortOrder();
        }
        $this->applyFilters();
        $this->fireInitializedEvent();
    }

    /**
     * Returns the database object list.
     */
    public function getObjectList(): DatabaseObjectList
    {
        if (!isset($this->objectList)) {
            $this->initObjectList();
        }

        return $this->objectList;
    }

    /**
     * Applies the active filters.
     */
    protected function applyFilters(): void
    {
        foreach ($this->getActiveFilters() as $key => $value) {
            $column = $this->getColumn($key);
            if (!$column) {
                throw new LogicException("Unknown column '" . $key . "'");
            }

            $column->getFilter()->applyFilter($this->getObjectList(), $column->getID(), $value);
        }
    }

    #[\Override]
    public function getObjectID(mixed $row): mixed
    {
        \assert($row instanceof DatabaseObject);

        return $row->getObjectID();
    }

    /**
     * Creates the database object list of this grid view.
     */
    protected abstract function createObjectList(): DatabaseObjectList;
}
