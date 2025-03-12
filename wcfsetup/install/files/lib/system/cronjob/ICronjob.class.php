<?php

namespace wcf\system\cronjob;

use wcf\data\cronjob\Cronjob;

/**
 * Any cronjob should implement this interface.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface ICronjob
{
    /**
     * Executes the cronjob.
     *
     * @return void
     */
    public function execute(Cronjob $cronjob);
}
