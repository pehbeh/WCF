<?php

namespace wcf\system\view\grid;

use LogicException;

abstract class ArrayGridView extends AbstractGridView
{
    protected array $dataArray;

    public function getRows(): array
    {
        $this->sortRows();

        return $this->getRowsForPage();
    }

    protected function sortRows(): void
    {
        $this->getDataArray();

        \uasort($this->dataArray, function (array $a, array $b) {
            if ($this->getSortOrder() === 'ASC') {
                return \strcmp($a[$this->getSortField()], $b[$this->getSortField()]);
            } else {
                return \strcmp($b[$this->getSortField()], $a[$this->getSortField()]);
            }
        });
    }

    protected function getRowsForPage(): array
    {
        return \array_slice($this->getDataArray(), ($this->getPageNo() - 1) * $this->getRowsPerPage(), $this->getRowsPerPage());
    }

    public function countRows(): int
    {
        return \count($this->getDataArray());
    }

    protected function getDataArray(): array
    {
        if (!isset($this->dataArray)) {
            $this->dataArray = $this->loadDataArray();
            $this->applyFilters();
            $this->fireInitializedEvent();
        }

        return $this->dataArray;
    }

    protected function applyFilters(): void
    {
        foreach ($this->getActiveFilters() as $key => $value) {
            $column = $this->getColumn($key);
            if (!$column) {
                throw new LogicException("Unknown column '" . $key . "'");
            }

            $this->dataArray = \array_filter($this->dataArray, function (array $row) use ($column, $value) {
                return $column->getFilter()->matches($value, $row[$column->getID()]);
            });
        }
    }

    protected abstract function loadDataArray(): array;
}
