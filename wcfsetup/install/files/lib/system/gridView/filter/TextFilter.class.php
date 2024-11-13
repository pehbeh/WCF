<?php

namespace wcf\system\gridView\filter;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\TextFormField;

/**
 * Filter for text columns.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class TextFilter implements IGridViewFilter
{
    #[\Override]
    public function getFormField(string $id, string $label): AbstractFormField
    {
        return TextFormField::create($id)
            ->label($label);
    }

    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $id, string $value): void
    {
        $list->getConditionBuilder()->add("$id LIKE ?", ['%' . $value . '%']);
    }

    #[\Override]
    public function matches(string $filterValue, string $rowValue): bool
    {
        return \str_contains(\mb_strtolower($rowValue), \mb_strtolower($filterValue));
    }

    #[\Override]
    public function renderValue(string $value): string
    {
        return $value;
    }
}
