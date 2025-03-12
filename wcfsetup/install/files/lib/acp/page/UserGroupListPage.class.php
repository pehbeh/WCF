<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\UserGroupGridView;

/**
 * Shows a list of all user groups.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<UserGroupGridView>
 */
final class UserGroupListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.group.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canEditGroup', 'admin.user.canDeleteGroup'];

    #[\Override]
    protected function createGridView(): UserGroupGridView
    {
        return new UserGroupGridView();
    }
}
