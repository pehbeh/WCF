<?php

namespace wcf\data;

/**
 * Every database object action whose objects can be toggled has to implement this
 * interface.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IToggleAction
{
    /**
     * Toggles the "isDisabled" status of the relevant objects.
     *
     * @return void
     */
    public function toggle();

    /**
     * Validates the "toggle" action.
     *
     * @return void
     */
    public function validateToggle();
}
