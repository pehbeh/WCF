<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\StyleGridView;

/**
 * Shows the style list page.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<StyleGridView>
 */
final class StyleListPage  extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.style.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.style.canManageStyle'];

    #[\Override]
    protected function createGridView(): StyleGridView
    {
        return new StyleGridView();
    }
}
