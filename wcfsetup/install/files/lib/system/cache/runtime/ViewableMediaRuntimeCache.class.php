<?php

namespace wcf\system\cache\runtime;

use wcf\data\media\ViewableMedia;
use wcf\data\media\ViewableMediaList;

/**
 * Runtime cache implementation for viewable media.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 *
 * @method  (ViewableMedia|null)[]         getCachedObjects()
 * @method  ?ViewableMedia      getObject($objectID)
 * @method  (ViewableMedia|null)[]         getObjects(array $objectIDs)
 */
class ViewableMediaRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = ViewableMediaList::class;
}
