<?php

namespace wcf\acp\page;

use wcf\page\AbstractGridViewPage;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\admin\PaidSubscriptionTransactionLogGridView;

/**
 * Shows the list of paid subscription transactions.
 *
 * @author      Olaf Braun, Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property    PaidSubscriptionTransactionLogGridView $gridView
 */
class PaidSubscriptionTransactionLogListPage extends AbstractGridViewPage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.paidSubscription.transactionLog.list';

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_PAID_SUBSCRIPTION'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.paidSubscription.canManageSubscription'];

    #[\Override]
    protected function createGridViewController(): AbstractGridView
    {
        return new PaidSubscriptionTransactionLogGridView();
    }
}
