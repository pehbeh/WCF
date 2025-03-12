<?php

namespace wcf\data\tag;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of tags.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<Tag>
 */
class TagList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Tag::class;
}
