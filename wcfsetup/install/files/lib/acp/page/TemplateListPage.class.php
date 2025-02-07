<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\TemplateGridView;

/**
 * Shows a list of templates.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    TemplateGridView $gridView
 */
class TemplateListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.template.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.template.canManageTemplate'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new TemplateGridView(TemplateGridView::DEFAULT_TEMPLATE_GROUP_ID);
    }
}
