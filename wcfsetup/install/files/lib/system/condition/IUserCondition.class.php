<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\user\User;
use wcf\data\user\UserList;

/**
 * Every implementation for user conditions needs to implements this interface.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IUserCondition extends ICondition
{
    /**
     * Adds the condition to the given user list to fetch the users which fulfill
     * the given condition.
     *
     * @return void
     */
    public function addUserCondition(Condition $condition, UserList $userList);

    /**
     * Returns true if the given user fulfills the given condition.
     *
     * @return bool
     */
    public function checkUser(Condition $condition, User $user);
}
