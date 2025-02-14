<?php

namespace wcf\system\cache\runtime;

use wcf\data\comment\response\ViewableCommentResponse;
use wcf\data\comment\response\ViewableCommentResponseList;

/**
 * Runtime cache implementation for viewable comment responses.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2021 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   5.5
 *
 * @method  (ViewableCommentResponse|null)[]   getCachedObjects()
 * @method  ?ViewableCommentResponse     getObject($objectID)
 * @method  (ViewableCommentResponse|null)[]   getObjects(array $objectIDs)
 */
class ViewableCommentResponseRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = ViewableCommentResponseList::class;
}
