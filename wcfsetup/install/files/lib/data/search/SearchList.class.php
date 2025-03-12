<?php

namespace wcf\data\search;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of searches.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Search>
 */
class SearchList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Search::class;
}
