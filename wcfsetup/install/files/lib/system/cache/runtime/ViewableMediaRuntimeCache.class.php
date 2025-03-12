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
 * @extends AbstractRuntimeCache<ViewableMedia, ViewableMediaList>
 */
class ViewableMediaRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = ViewableMediaList::class;
}
