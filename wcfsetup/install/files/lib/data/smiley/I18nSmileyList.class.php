<?php

namespace wcf\data\smiley;

use wcf\data\I18nDatabaseObjectList;

/**
 * Represents a list of smilies.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends I18nDatabaseObjectList<Smiley>
 */
class I18nSmileyList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['smileyTitle' => 'smileyTitleI18n'];

    /**
     * @inheritDoc
     */
    public $className = Smiley::class;
}
