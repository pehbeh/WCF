<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\view\grid\AbstractGridView;
use wcf\system\view\grid\UserOptionGridView;

/**
 * Shows a list of the installed user options.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    UserOptionGridView    $gridView
 */
class UserOptionListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.user.option.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canManageUserOption'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new UserOptionGridView();
    }
}
