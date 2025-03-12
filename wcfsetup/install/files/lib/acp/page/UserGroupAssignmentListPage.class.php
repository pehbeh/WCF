<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\UserGroupAssignmentGridView;

/**
 * Lists the available automatic user group assignments.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<UserGroupAssignmentGridView>
 */
class UserGroupAssignmentListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.assignment';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canManageGroupAssignment'];

    #[\Override]
    protected function createGridView(): UserGroupAssignmentGridView
    {
        return new UserGroupAssignmentGridView();
    }
}
