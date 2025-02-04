<?php

namespace wcf\data\label\group;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of label group list.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @method      LabelGroup        current()
 * @method      LabelGroup[]      getObjects()
 * @method      LabelGroup|null   getSingleObject()
 * @method      LabelGroup|null   search($objectID)
 * @property    LabelGroup[] $objects
 */
class I18nLabelGroupList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['groupName' => 'groupNameI18n'];

    /**
     * @inheritDoc
     */
    public $className = LabelGroup::class;
}
