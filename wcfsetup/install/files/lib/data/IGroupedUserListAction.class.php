<?php

namespace wcf\data;

/**
 * Default interface for action classes providing grouped user lists.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IGroupedUserListAction
{
    /**
     * Validates parameters to return a parsed list of users.
     *
     * @return void
     */
    public function validateGetGroupedUserList();

    /**
     * Returns a parsed list of users.
     *
     * @return array{pageCount: int, template: string}
     */
    public function getGroupedUserList();
}
