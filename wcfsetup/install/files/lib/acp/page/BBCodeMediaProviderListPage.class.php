<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\admin\BBCodeMediaProviderGridView;

/**
 * Lists the available media providers.
 *
 * @author      Olaf Braun, Tim Duesterhus
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<BBCodeMediaProviderGridView>
 */
class BBCodeMediaProviderListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.bbcode.mediaProvider.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.content.bbcode.canManageBBCode'];

    /**
     * @inheritDoc
     */
    public $templateName = 'bbcodeMediaProviderList';

    #[\Override]
    protected function createGridView(): BBCodeMediaProviderGridView
    {
        return new BBCodeMediaProviderGridView();
    }
}
