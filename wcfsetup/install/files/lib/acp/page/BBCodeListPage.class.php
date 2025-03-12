<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\BBCodeGridView;

/**
 * Lists the available BBCodes.
 *
 * @author      Olaf Braun, Tim Duesterhus
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<BBCodeGridView>
 */
final class BBCodeListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.bbcode.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.bbcode.canManageBBCode'];

    /**
     * @inheritDoc
     */
    public $templateName = 'bbcodeList';

    #[\Override]
    public function createGridView(): BBCodeGridView
    {
        return new BBCodeGridView();
    }
}
