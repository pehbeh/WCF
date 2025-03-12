<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\TemplateGroupGridView;

/**
 * Shows a list of installed template groups.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<TemplateGroupGridView>
 */
class TemplateGroupListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.template.group.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.template.canManageTemplate'];

    #[\Override]
    protected function createGridView(): TemplateGroupGridView
    {
        return new TemplateGroupGridView();
    }
}
