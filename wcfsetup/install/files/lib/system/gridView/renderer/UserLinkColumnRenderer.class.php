<?php

namespace wcf\system\gridView\renderer;

use wcf\system\cache\runtime\AbstractRuntimeCache;
use wcf\system\cache\runtime\UserRuntimeCache;

/**
 * Formats the content of a column as a user link. The value of the column must be a user id.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
class UserLinkColumnRenderer extends ObjectLinkColumnRenderer
{
    #[\Override]
    protected function getRuntimeCache(): AbstractRuntimeCache
    {
        return UserRuntimeCache::getInstance();
    }
}
