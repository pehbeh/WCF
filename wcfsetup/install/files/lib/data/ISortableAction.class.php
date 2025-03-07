<?php

namespace wcf\data;

/**
 * Every object action whose objects can be sorted via AJAX has to implement this
 * interface.
 *
 * @author  Alexander Ebert, Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ISortableAction
{
    /**
     * Validates the 'updatePosition' action.
     *
     * @return void
     */
    public function validateUpdatePosition();

    /**
     * Updates the position of given objects.
     *
     * @return void
     */
    public function updatePosition();
}
