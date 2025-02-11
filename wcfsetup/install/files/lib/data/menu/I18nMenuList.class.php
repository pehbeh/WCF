<?php

namespace wcf\data\menu;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of menus.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @method  Menu        current()
 * @method  Menu[]      getObjects()
 * @method  Menu|null   getSingleObject()
 * @method  Menu|null   search($objectID)
 * @property    Menu[] $objects
 */
class I18nMenuList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ["title" => "titleI18n"];

    /**
     * @inheritDoc
     */
    public $className = Menu::class;
}
