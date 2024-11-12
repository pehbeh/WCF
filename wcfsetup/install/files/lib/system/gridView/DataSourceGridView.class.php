<?php

namespace wcf\system\gridView;

use LogicException;

abstract class DataSourceGridView extends AbstractGridView
{
    protected array $dataSource;

    public function getRows(): array
    {
        $this->sortRows();

        return $this->getRowsForPage();
    }

    protected function sortRows(): void
    {
        $this->getDataSource();

        \uasort($this->dataSource, function (array $a, array $b) {
            if ($this->getSortOrder() === 'ASC') {
                return \strcmp($a[$this->getSortField()], $b[$this->getSortField()]);
            } else {
                return \strcmp($b[$this->getSortField()], $a[$this->getSortField()]);
            }
        });
    }

    protected function getRowsForPage(): array
    {
        return \array_slice($this->getDataSource(), ($this->getPageNo() - 1) * $this->getRowsPerPage(), $this->getRowsPerPage());
    }

    public function countRows(): int
    {
        return \count($this->getDataSource());
    }

    protected function getDataSource(): array
    {
        if (!isset($this->dataSource)) {
            $this->dataSource = $this->loadDataSource();
            $this->applyFilters();
            $this->fireInitializedEvent();
        }

        return $this->dataSource;
    }

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

    protected abstract function loadDataSource(): array;
}
