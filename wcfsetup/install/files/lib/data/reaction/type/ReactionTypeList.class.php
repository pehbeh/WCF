<?php

namespace wcf\data\reaction\type;

use wcf\data\DatabaseObjectList;

/**
 * Represents a reaction type list.
 *
 * @author  Joshua Ruesweg
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.2
 *
 * @extends DatabaseObjectList<ReactionType>
 */
class ReactionTypeList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'reaction_type.showOrder ASC';
}
