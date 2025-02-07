<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\UserAuthenticationFailureGridView;

/**
 * Shows a list of user authentication failures.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    UserAuthenticationFailureGridView $gridView
 */
class UserAuthenticationFailureListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.log.authentication.failure';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.management.canViewLog'];

    /**
     * @inheritDoc
     */
    public $neededModules = ['ENABLE_USER_AUTHENTICATION_FAILURE'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new UserAuthenticationFailureGridView();
    }
}
