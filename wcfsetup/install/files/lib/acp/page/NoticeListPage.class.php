<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\NoticeGridView;

/**
 * Lists the available notices.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<NoticeGridView>
 */
class NoticeListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.notice.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.notice.canManageNotice'];

    #[\Override]
    protected function createGridView(): AbstractGridView
    {
        return new NoticeGridView();
    }
}
