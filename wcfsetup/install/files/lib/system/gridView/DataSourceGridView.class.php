<?php

namespace wcf\system\gridView;

use LogicException;

/**
 * Abstract implementation of a grid view that uses an array as the data source.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
abstract class DataSourceGridView extends AbstractGridView
{
    protected array $dataSource;

    #[\Override]
    public function getRows(): array
    {
        $this->sortRows();

        return $this->getRowsForPage();
    }

    /**
     * Sorts the rows.
     */
    protected function sortRows(): void
    {
        // Necessary to ensure that dataSource has been initialized.
        $this->getDataSource();

        \uasort($this->dataSource, function (array $a, array $b) {
            if ($this->getSortOrder() === 'ASC') {
                return \strcmp($a[$this->getSortField()], $b[$this->getSortField()]);
            } else {
                return \strcmp($b[$this->getSortField()], $a[$this->getSortField()]);
            }
        });
    }

    /**
     * Returns the rows for the active page.
     */
    protected function getRowsForPage(): array
    {
        return \array_slice($this->getDataSource(), ($this->getPageNo() - 1) * $this->getRowsPerPage(), $this->getRowsPerPage());
    }

    #[\Override]
    public function countRows(): int
    {
        return \count($this->getDataSource());
    }

    /**
     * Returns the data source array.
     */
    protected function getDataSource(): array
    {
        if (!isset($this->dataSource)) {
            $this->dataSource = $this->loadDataSource();
            $this->applyFilters();
            $this->validate();
            $this->fireInitializedEvent();
        }

        return $this->dataSource;
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

            $this->dataSource = \array_filter($this->dataSource, function (array $row) use ($column, $value) {
                return $column->getFilter()->matches($value, $row[$column->getID()]);
            });
        }
    }

    /**
     * Loads the data source array.
     */
    protected abstract function loadDataSource(): array;
}
