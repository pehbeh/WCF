<?php

namespace wcf\data\template\group;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of template group list.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @extends I18nDatabaseObjectList<TemplateGroup>
 */
class I18nTemplateGroupList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['templateGroupName' => 'templateGroupNameI18n'];

    /**
     * @inheritDoc
     */
    public $className = TemplateGroup::class;
}
