<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\AdGridView;

/**
 * Lists the available ads.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractGridViewPage<AdGridView>
 */
class AdListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.ad.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.ad.canManageAd'];

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_WCF_AD'];

    #[\Override]
    protected function createGridView(): AbstractGridView
    {
        return new AdGridView();
    }
}
