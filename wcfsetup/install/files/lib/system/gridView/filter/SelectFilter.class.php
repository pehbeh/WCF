<?php

namespace wcf\system\gridView\filter;

use wcf\data\DatabaseObjectList;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\SelectFormField;
use wcf\system\WCF;

/**
 * Allows a column to be filtered on the basis of a select dropdown.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class SelectFilter implements IGridViewFilter
{
    public function __construct(private readonly array $options) {}

    #[\Override]
    public function getFormField(string $id, string $label): AbstractFormField
    {
        return SelectFormField::create($id)
            ->label($label)
            ->options($this->options);
    }

    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $id, string $value): void
    {
        $list->getConditionBuilder()->add("$id = ?", [$value]);
    }

    #[\Override]
    public function matches(string $filterValue, string $rowValue): bool
    {
        return $filterValue === $rowValue;
    }

    #[\Override]
    public function renderValue(string $value): string
    {
        return WCF::getLanguage()->get($this->options[$value]);
    }
}
