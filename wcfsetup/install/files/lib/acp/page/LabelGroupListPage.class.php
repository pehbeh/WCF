<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\LabelGroupGridView;

/**
 * Lists available label groups.
 *
 * @author      Olaf Braun, Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    LabelGroupGridView $gridView
 */
class LabelGroupListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.label.group.list';

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new LabelGroupGridView();
    }
}
