<?php

namespace wcf\data;

/**
 * Default interface for objects supporting visit tracking.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IVisitableObjectAction
{
    /**
     * Marks objects as read.
     *
     * @return void
     */
    public function markAsRead();

    /**
     * Validates parameters to mark objects as read.
     *
     * @return void
     */
    public function validateMarkAsRead();
}
