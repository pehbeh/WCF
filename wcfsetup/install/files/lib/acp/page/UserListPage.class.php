<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\UserGridView;

/**
 * Shows the result of a user search.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property UserGridView $gridView
 */
class UserListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.user.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canSearchUser'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new UserGridView();
    }
}
