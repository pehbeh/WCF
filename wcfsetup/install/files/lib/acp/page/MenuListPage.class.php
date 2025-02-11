<?php

namespace wcf\acp\page;

use wcf\data\menu\MenuList;
use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\MenuGridView;

/**
 * Shows a list of menus.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       3.0
 *
 * @property    MenuList $objectList
 */
class MenuListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.cms.menu.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.cms.canManageMenu'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new MenuGridView();
    }
}
