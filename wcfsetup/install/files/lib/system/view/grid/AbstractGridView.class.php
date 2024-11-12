<?php

namespace wcf\system\view\grid;

use LogicException;
use wcf\action\GridViewFilterAction;
use wcf\event\IPsr14Event;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\view\grid\action\IGridViewAction;
use wcf\system\WCF;

abstract class AbstractGridView
{
    /**
     * @var GridViewColumn[]
     */
    private array $columns = [];

    /**
     * @var IGridViewAction[]
     */
    private array $actions = [];

    private GridViewRowLink $rowLink;
    private int $rowsPerPage = 20;
    private string $baseUrl = '';
    private string $sortField = '';
    private string $sortOrder = 'ASC';
    private int $pageNo = 1;
    private array $activeFilters = [];

    /**
     * Adds a new column to the grid view.
     */
    public function addColumn(GridViewColumn $column): void
    {
        $this->columns[] = $column;
    }

    /**
     * Adds a new column to the grid view at the position before the specific id.
     */
    public function addColumnBefore(GridViewColumn $column, string $beforeID): void
    {
        $position = -1;

        foreach ($this->getColumns() as $key => $existingColumn) {
            if ($existingColumn->getID() === $beforeID) {
                $position = $key;
                break;
            }
        }

        if ($position === -1) {
            throw new \InvalidArgumentException("Invalid column id '{$beforeID}' given.");
        }

        array_splice($this->columns, $position, 0, [
            $column,
        ]);
    }

    /**
     * Adds a new column to the grid view at the position after the specific id.
     */
    public function addColumnAfter(GridViewColumn $column, string $afterID): void
    {
        $position = -1;

        foreach ($this->getColumns() as $key => $existingColumn) {
            if ($existingColumn->getID() === $afterID) {
                $position = $key;
                break;
            }
        }

        if ($position === -1) {
            throw new \InvalidArgumentException("Invalid column id '{$afterID}' given.");
        }

        array_splice($this->columns, $position + 1, 0, [
            $column,
        ]);
    }

    /**
     * Adds multiple new columns to the grid view.
     * @param GridViewColumn[] $columns
     */
    public function addColumns(array $columns): void
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * Returns all columns of the grid view.
     * @return GridViewColumn[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Returns all visible (non-hidden) columns of the grid view.
     * @return GridViewColumn[]
     */
    public function getVisibleColumns(): array
    {
        return \array_filter($this->getColumns(), fn($column) => !$column->isHidden());
    }

    /**
     * Returns the column with the given id or null if no such column exists.
     */
    public function getColumn(string $id): ?GridViewColumn
    {
        foreach ($this->getColumns() as $column) {
            if ($column->getID() === $id) {
                return $column;
            }
        }

        return null;
    }

    /**
     * @param IGridViewAction[] $columns
     */
    public function addActions(array $actions): void
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }
    }

    public function addAction(IGridViewAction $action): void
    {
        $this->actions[] = $action;
    }

    /**
     * @return IGridViewAction[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    public function hasActions(): bool
    {
        return $this->actions !== [];
    }

    public function hasDropdownActions(): bool
    {
        return $this->getDropdownActions() !== [];
    }

    /**
     * @return IGridViewAction[]
     */
    public function getDropdownActions(): array
    {
        return \array_filter($this->getActions(), fn($action) => !$action->isQuickAction());
    }

    /**
     * @return IGridViewAction[]
     */
    public function getQuickActions(): array
    {
        return \array_filter($this->getActions(), fn($action) => $action->isQuickAction());
    }

    public function render(): string
    {
        return WCF::getTPL()->fetch('shared_gridView', 'wcf', ['view' => $this], true);
    }

    public function renderRows(): string
    {
        return WCF::getTPL()->fetch('shared_gridViewRows', 'wcf', ['view' => $this], true);
    }

    public function renderColumn(GridViewColumn $column, mixed $row): string
    {
        $value = $column->render($this->getData($row, $column->getID()), $row);

        if (isset($this->rowLink)) {
            $value = $this->rowLink->render($value, $row, $column->isTitleColumn());
        }

        return $value;
    }

    public function renderAction(IGridViewAction $action, mixed $row): string
    {
        return $action->render($row);
    }

    public function renderActionInitialization(): string
    {
        return implode(
            "\n",
            \array_map(
                fn($action) => $action->renderInitialization($this),
                $this->getActions()
            )
        );
    }

    protected function getData(mixed $row, string $identifer): mixed
    {
        return $row[$identifer] ?? '';
    }

    public abstract function getRows(): array;

    public abstract function countRows(): int;

    public function countPages(): int
    {
        return \ceil($this->countRows() / $this->getRowsPerPage());
    }

    public function getClassName(): string
    {
        return \get_class($this);
    }

    public function isAccessible(): bool
    {
        return true;
    }

    public function getID(): string
    {
        $classNamePieces = \explode('\\', static::class);

        return \implode('-', $classNamePieces);
    }

    public function setBaseUrl(string $url): void
    {
        $this->baseUrl = $url;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return GridViewColumn[]
     */
    public function getSortableColumns(): array
    {
        return \array_filter($this->getColumns(), fn($column) => $column->isSortable());
    }

    /**
     * @return GridViewColumn[]
     */
    public function getFilterableColumns(): array
    {
        return \array_filter($this->getColumns(), fn($column) => $column->getFilter() !== null);
    }

    public function setSortField(string $sortField): void
    {
        if (!\in_array($sortField, \array_map(fn($column) => $column->getID(), $this->getSortableColumns()))) {
            throw new \InvalidArgumentException("Invalid value '{$sortField}' as sort field given.");
        }

        $this->sortField = $sortField;
    }

    public function setSortOrder(string $sortOrder): void
    {
        if ($sortOrder !== 'ASC' && $sortOrder !== 'DESC') {
            throw new \InvalidArgumentException("Invalid value '{$sortOrder}' as sort order given.");
        }

        $this->sortOrder = $sortOrder;
    }

    public function getSortField(): string
    {
        return $this->sortField;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    public function getPageNo(): int
    {
        return $this->pageNo;
    }

    public function setPageNo(int $pageNo): void
    {
        $this->pageNo = $pageNo;
    }

    public function getRowsPerPage(): int
    {
        return $this->rowsPerPage;
    }

    public function setRowsPerPage(int $rowsPerPage): void
    {
        $this->rowsPerPage = $rowsPerPage;
    }

    public function isFilterable(): bool
    {
        return $this->getFilterableColumns() !== [];
    }

    public function getFilterActionEndpoint(): string
    {
        return LinkHandler::getInstance()->getControllerLink(
            GridViewFilterAction::class,
            ['gridView' => \get_class($this)]
        );
    }

    public function setActiveFilters(array $filters): void
    {
        $this->activeFilters = $filters;
    }

    public function getActiveFilters(): array
    {
        return $this->activeFilters;
    }

    public function getFilterLabel(string $id): string
    {
        $column = $this->getColumn($id);
        if (!$column) {
            throw new LogicException("Unknown column '" . $id . "'.");
        }

        if (!$column->getFilter()) {
            throw new LogicException("Column '" . $id . "' has no filter.");
        }

        if (!isset($this->activeFilters[$id])) {
            throw new LogicException("No value for filter '" . $id . "' found.");
        }

        return $column->getLabel() . ': ' . $column->getFilter()->renderValue($this->activeFilters[$id]);
    }

    public function getParameters(): array
    {
        return [];
    }

    public function addRowLink(GridViewRowLink $rowLink): void
    {
        $this->rowLink = $rowLink;
    }

    public function getObjectID(mixed $row): mixed
    {
        return '';
    }

    protected function fireInitializedEvent(): void
    {
        $event = $this->getInitializedEvent();
        if ($event === null) {
            return;
        }

        EventHandler::getInstance()->fire($event);
    }

    protected function getInitializedEvent(): ?IPsr14Event
    {
        return null;
    }
}
