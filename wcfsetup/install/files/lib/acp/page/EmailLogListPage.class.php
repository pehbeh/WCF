<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\EmailLogGridView;

/**
 * Shows email logs.
 *
 * @author      Olaf Braun, Tim Duesterhus
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    EmailLogGridView $gridView
 */
class EmailLogListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.log.email';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.management.canViewLog'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new EmailLogGridView();
    }
}
