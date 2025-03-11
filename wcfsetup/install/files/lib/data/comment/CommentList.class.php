<?php

namespace wcf\data\comment;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of comments.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @template-covariant TDatabaseObject of Comment|DatabaseObjectDecorator<Comment> = Comment
 * @extends DatabaseObjectList<TDatabaseObject>
 */
class CommentList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Comment::class;
}
