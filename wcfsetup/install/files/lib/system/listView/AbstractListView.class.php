<?php

namespace wcf\system\listView;

use wcf\action\ListViewFilterAction;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\system\listView\filter\IListViewFilter;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

abstract class AbstractListView
{
    private int $objectCount;
    private DatabaseObjectList $objectList;
    private int $itemsPerPage = 20;
    private string $baseUrl = '';
    private string $sortField = '';
    private string $sortOrder = 'ASC';
    private int $pageNo = 1;

    /**
     * @var array<string, string>
     */
    private array $activeFilters = [];

    /**
     * @var array<string, ListViewSortField>
     */
    private array $availableSortFields = [];

    /**
     * @var array<string, IListViewFilter>
     */
    private array $availableFilters = [];

    /**
     * @var DatabaseObject[]
     */
    private array $objects;

    /**
     * Returns the number of items per page.
     */
    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    /**
     * Sets the number of items per page.
     */
    public function setItemsPerPage(int $itemsPerPage): void
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Sets the sort field of the list view.
     */
    public function setSortField(string $sortField): void
    {
        if (!isset($this->availableSortFields[$sortField])) {
            throw new \InvalidArgumentException("Invalid value '{$sortField}' as sort field given.");
        }

        $this->sortField = $sortField;
    }

    /**
     * Sets the sort order of the list view.
     */
    public function setSortOrder(string $sortOrder): void
    {
        if ($sortOrder !== 'ASC' && $sortOrder !== 'DESC') {
            throw new \InvalidArgumentException("Invalid value '{$sortOrder}' as sort order given.");
        }

        $this->sortOrder = $sortOrder;
    }

    /**
     * Returns the sort field of the list view.
     */
    public function getSortField(): string
    {
        return $this->sortField;
    }

    /**
     * Returns the sort order of the list view.
     */
    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    /**
     * Returns the page number.
     */
    public function getPageNo(): int
    {
        return $this->pageNo;
    }

    /**
     * Sets the page number.
     */
    public function setPageNo(int $pageNo): void
    {
        $this->pageNo = $pageNo;
    }

    /**
     * Sets the active filter values.
     */
    public function setActiveFilters(array $filters): void
    {
        $this->activeFilters = $filters;
    }

    /**
     * Returns the active filter values.
     */
    public function getActiveFilters(): array
    {
        return $this->activeFilters;
    }

    /**
     * Sets the base url of the list view.
     */
    public function setBaseUrl(string $url): void
    {
        $this->baseUrl = $url;
    }

    /**
     * Returns the base url of the list view.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Initializes the database object list.
     */
    protected function initObjectList(): void
    {
        $this->objectList = $this->createObjectList();
        $this->objectList->sqlLimit = $this->getItemsPerPage();
        $this->objectList->sqlOffset = ($this->getPageNo() - 1) * $this->getItemsPerPage();
        if ($this->getSortField()) {
            $sortFieldObject = $this->availableSortFields[$this->getSortField()];

            if ($sortFieldObject->sortByDatabaseColumn) {
                $this->objectList->sqlOrderBy = $sortFieldObject->sortByDatabaseColumn . ' ' . $this->getSortOrder();
            } else {
                $this->objectList->sqlOrderBy = $this->objectList->getDatabaseTableAlias() .
                    '.' . $sortFieldObject->id . ' ' . $this->getSortOrder();
            }

            $this->objectList->sqlOrderBy .= ',' . $this->objectList->getDatabaseTableAlias() .
                '.' . $this->objectList->getDatabaseTableIndexName() . ' ' . $this->getSortOrder();
        }
        /*if ($this->getObjectIDFilter() !== null) {
            $this->objectList->getConditionBuilder()->add(
                $this->objectList->getDatabaseTableAlias() . '.' . $this->objectList->getDatabaseTableIndexName() . ' = ?',
                [$this->getObjectIDFilter()]
            );
        }*/
        $this->applyFilters();
        /*$this->validate();
        $this->fireInitializedEvent();
        */
    }

    /**
     * Applies the active filters.
     */
    protected function applyFilters(): void
    {
        foreach ($this->getActiveFilters() as $key => $value) {
            if (!isset($this->availableFilters[$key])) {
                throw new \LogicException("Unknown filter '" . $key . "'");
            }

            $this->availableFilters[$key]->applyFilter($this->getObjectList(), $value);
        }
    }

    /**
     * Returns the items for the active page.
     *
     * @return DatabaseObject[]
     */
    public function getItems(): array
    {
        if (!isset($this->objects)) {
            $this->getObjectList()->readObjects();
            $this->objects = $this->getObjectList()->getObjects();
        }

        return $this->objects;
    }

    /**
     * Returns the total number of items.
     */
    public function countItems(): int
    {
        if (!isset($this->objectCount)) {
            $this->objectCount = $this->getObjectList()->countObjects();
        }

        return $this->objectCount;
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
     * Counts the pages of the grid view.
     */
    public function countPages(): int
    {
        return (int)\ceil($this->countItems() / $this->getItemsPerPage());
    }

    /**
     * Returns the class name of this list view.
     */
    public function getClassName(): string
    {
        return \get_class($this);
    }

    /**
     * Returns true, if this list view is accessible for the active user.
     */
    public function isAccessible(): bool
    {
        return true;
    }

    /**
     * Returns the id of this list view.
     */
    public function getID(): string
    {
        $classNamePieces = \explode('\\', static::class);

        return \implode('-', $classNamePieces);
    }

    /**
     * Returns true, if the list view is filterable.
     */
    public function isFilterable(): bool
    {
        return $this->availableFilters !== [];
    }

    /**
     * Returns the endpoint for the filter action.
     */
    public function getFilterActionEndpoint(): string
    {
        return LinkHandler::getInstance()->getControllerLink(
            ListViewFilterAction::class,
            ['listView' => \get_class($this), 'listViewParameters' => $this->getParameters()]
        );
    }

    /**
     * Returns true, if the list view is sortable.
     */
    public function isSortable(): bool
    {
        return $this->availableSortFields !== [];
    }

    public function addAvailableSortField(ListViewSortField $sortField): void
    {
        $this->availableSortFields[$sortField->id] = $sortField;
    }

    /**
     * @param array<string, ListViewSortField>
     */
    public function addAvailableSortFields(array $sortFields): void
    {
        foreach ($sortFields as $sortField) {
            $this->addAvailableSortField($sortField);
        }
    }

    /**
     * @return ListViewSortField[]
     */
    public function getAvailableSortFields(): array
    {
        return $this->availableSortFields;
    }

    public function addAvailableFilter(IListViewFilter $filter): void
    {
        $this->availableFilters[$filter->getId()] = $filter;
    }

    /**
     * @param IListViewFilter[] $filters
     */
    public function addAvailableFilters(array $filters): void
    {
        foreach ($filters as $filter) {
            $this->addAvailableFilter($filter);
        }
    }

    /**
     * @return array<string, IListViewFilter>
     */
    public function getAvailableFilters(): array
    {
        return $this->availableFilters;
    }

    /**
     * Gets the additional parameters of the list view.
     *
     * @return mixed[]
     */
    public function getParameters(): array
    {
        return [];
    }

    /**
     * Returns the label for the given filter.
     */
    public function getFilterLabel(string $id): string
    {
        if (!isset($this->availableFilters[$id])) {
            throw new \LogicException("Unknown filter '" . $id . "'.");
        }

        if (!isset($this->activeFilters[$id])) {
            throw new \LogicException("No value for filter '" . $id . "' found.");
        }

        $value = $this->availableFilters[$id]->renderValue($this->activeFilters[$id]);

        return $this->availableFilters[$id]->getLabel() . ($value !== '' ? ': ' . $value : '');
    }

    public function render(): string
    {
        return WCF::getTPL()->render('wcf', 'shared_listView', ['view' => $this]);
    }

    protected abstract function createObjectList(): DatabaseObjectList;

    public abstract function renderItems(): string;
}
