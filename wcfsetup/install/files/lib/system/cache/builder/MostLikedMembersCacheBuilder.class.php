<?php

namespace wcf\system\cache\builder;

/**
 * Caches a list of the most liked members.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @deprecated  6.2 use `SortedUserCache` instead
 */
class MostLikedMembersCacheBuilder extends AbstractSortedUserCacheBuilder
{
    /**
     * @inheritDoc
     */
    protected $positiveValuesOnly = true;

    /**
     * @inheritDoc
     */
    protected $sortField = 'likesReceived';
}
