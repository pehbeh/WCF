<?php

namespace wcf\data\like;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of likes.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Like>
 */
class LikeList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Like::class;
}
