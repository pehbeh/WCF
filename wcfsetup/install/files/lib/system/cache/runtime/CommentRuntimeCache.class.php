<?php

namespace wcf\system\cache\runtime;

use wcf\data\comment\Comment;
use wcf\data\comment\CommentList;

/**
 * Runtime cache implementation for comments.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 *
 * @method  (Comment|null)[]    getCachedObjects()
 * @method  ?Comment getObject($objectID)
 * @method  (Comment|null)[]    getObjects(array $objectIDs)
 */
class CommentRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = CommentList::class;
}
