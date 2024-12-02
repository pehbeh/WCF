<?php

namespace wcf\data\user\cover\photo;

/**
 * Any displayable cover photo type should implement this class.
 *
 * @author Alexander Ebert
 * @copyright 2001-2021 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 5.4
 * @deprecated 6.2
 */
interface IWebpUserCoverPhoto extends IUserCoverPhoto
{
    /**
     * @return null|bool
     */
    public function createWebpVariant();
}
