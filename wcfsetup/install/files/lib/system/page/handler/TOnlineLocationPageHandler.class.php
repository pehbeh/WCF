<?php

namespace wcf\system\page\handler;

use wcf\data\page\Page;
use wcf\data\user\online\UserOnline;

/**
 * Default implementation of the interface for pages supporting online location.
 *
 * It is highly recommended to use this trait in any case to achieve better upwards-compatibility
 * in case of interface changes.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
trait TOnlineLocationPageHandler
{
    /**
     * @return string
     */
    public function getOnlineLocation(Page $page, UserOnline $user)
    {
        return '';
    }

    /**
     * @return void
     */
    public function prepareOnlineLocation(Page $page, UserOnline $user)
    {
        // does nothing
    }
}
