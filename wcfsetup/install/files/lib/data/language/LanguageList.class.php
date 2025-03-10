<?php

namespace wcf\data\language;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of languages.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Language>
 */
class LanguageList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Language::class;
}
