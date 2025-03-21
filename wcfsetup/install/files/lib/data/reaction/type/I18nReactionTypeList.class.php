<?php

namespace wcf\data\reaction\type;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of a reaction type list.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends I18nDatabaseObjectList<ReactionType>
 */
class I18nReactionTypeList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ["title" => "titleI18n"];

    /**
     * @inheritDoc
     */
    public $className = ReactionType::class;
}
