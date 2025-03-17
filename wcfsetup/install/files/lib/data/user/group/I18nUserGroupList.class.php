<?php

namespace wcf\data\user\group;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of user group list.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.2
 *
 * @extends I18nDatabaseObjectList<UserGroup>
 */
class I18nUserGroupList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['groupName' => 'groupNameI18n'];

    /**
     * @inheritDoc
     */
    public $className = UserGroup::class;
}
