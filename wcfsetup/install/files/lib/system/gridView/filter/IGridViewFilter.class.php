<?php

namespace wcf\system\gridView\filter;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\AbstractFormField;

interface IGridViewFilter
{
    public function getFormField(string $id, string $label): AbstractFormField;

    public function applyFilter(DatabaseObjectList $list, string $id, string $value): void;

    public function matches(string $filterValue, string $rowValue): bool;

    public function renderValue(string $value): string;
}
