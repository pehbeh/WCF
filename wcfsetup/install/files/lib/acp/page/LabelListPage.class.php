<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\LabelGridView;

/**
 * Lists available labels
 *
 * @author  Olaf Braun, Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<LabelGridView>
 */
final class LabelListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.label.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.label.canManageLabel'];

    #[\Override]
    protected function createGridView(): LabelGridView
    {
        return new LabelGridView();
    }
}
