<?php

namespace wcf\system\cache\runtime;

use wcf\data\comment\ViewableComment;
use wcf\data\comment\ViewableCommentList;

/**
 * Runtime cache implementation for viewable comments.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2021 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.5
 *
 * @method  (ViewableComment|null)[]   getCachedObjects()
 * @method  ViewableComment|null     getObject($objectID)
 * @method  (ViewableComment|null)[]   getObjects(array $objectIDs)
 */
class ViewableCommentRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = ViewableCommentList::class;
}
