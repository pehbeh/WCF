<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\SmileyGridView;

/**
 * Lists the available smilies.
 *
 * @author  Olaf Braun, Tim Duesterhus
 * @copyright   2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<SmileyGridView>
 */
final class SmileyListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.smiley.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.smiley.canManageSmiley'];

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_SMILEY'];

    /**
     * @inheritDoc
     */
    public $templateName = 'smileyList';

    #[\Override]
    protected function createGridView(): SmileyGridView
    {
        return new SmileyGridView();
    }
}
