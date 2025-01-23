<?php

namespace wcf\system\gridView\filter;

use wcf\data\DatabaseObjectList;
use wcf\system\cache\runtime\UserRuntimeCache;
use wcf\system\form\builder\field\AbstractFormField;
use wcf\system\form\builder\field\user\UserFormField;

/**
 * Filter for columns that contain user ids.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class UserFilter implements IGridViewFilter
{
    #[\Override]
    public function getFormField(string $id, string $label): AbstractFormField
    {
        return UserFormField::create($id)
            ->label($label)
            ->nullable();
    }

    #[\Override]
    public function applyFilter(DatabaseObjectList $list, string $id, string $value): void
    {
        $list->getConditionBuilder()->add("{$id} = ?", [$value]);
    }

    #[\Override]
    public function matches(string $filterValue, string $rowValue): bool
    {
        return $rowValue == $filterValue;
    }

    #[\Override]
    public function renderValue(string $value): string
    {
        $user = UserRuntimeCache::getInstance()->getObject($value);

        return $user ? $user->username : '';
    }
}
