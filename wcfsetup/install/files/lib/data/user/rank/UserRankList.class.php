<?php

namespace wcf\data\user\rank;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of user ranks.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @template-covariant TDatabaseObject of UserRank|DatabaseObjectDecorator<UserRank> = UserRank
 * @extends DatabaseObjectList<TDatabaseObject>
 */
class UserRankList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = UserRank::class;
}
