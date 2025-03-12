<?php

namespace wcf\data\paid\subscription;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of paid subscriptions.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends I18nDatabaseObjectList<PaidSubscription>
 */
class I18nPaidSubscriptionList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['title' => 'titleI18n'];

    /**
     * @inheritDoc
     */
    public $className = PaidSubscription::class;
}
