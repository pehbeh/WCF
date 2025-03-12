<?php

namespace wcf\data;

/**
 * Every database object action class, which belongs to database objects supporting
 * clipboard actions, has to implement this interface.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IClipboardAction
{
    /**
     * Unmarks all marked objects.
     *
     * @return void
     */
    public function unmarkAll();

    /**
     * Validates the 'unmarkAll' action.
     *
     * @return void
     */
    public function validateUnmarkAll();
}
