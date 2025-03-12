<?php

namespace wcf\data\search\keyword;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of keywords.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<SearchKeyword>
 */
class SearchKeywordList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = SearchKeyword::class;
}
