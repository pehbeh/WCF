<?php

namespace wcf\data\package;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of the package list.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 *
 * @extends I18nDatabaseObjectList<Package>
 */
class I18nPackageList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['packageName' => 'packageNameI18n'];

    /**
     * @inheritDoc
     */
    public $className = Package::class;
}
