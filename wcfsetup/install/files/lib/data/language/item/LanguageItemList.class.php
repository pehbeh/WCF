<?php

namespace wcf\data\language\item;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of language items.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<LanguageItem>
 */
class LanguageItemList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = LanguageItem::class;
}
