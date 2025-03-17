<?php

namespace wcf\system\cli\command;

/**
 * Every command has to implement this interface.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ICLICommand
{
    /**
     * Executes the command.
     *
     * @param mixed[] $parameters
     * @return void
     */
    public function execute(array $parameters);

    /**
     * Returns true if the user is allowed to use this command.
     *
     * @return bool
     */
    public function canAccess();
}
