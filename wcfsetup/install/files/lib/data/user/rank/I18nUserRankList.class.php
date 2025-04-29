<?php

namespace wcf\data\user\rank;

use wcf\data\I18nDatabaseObjectList;

/**
 * I18n implementation of user rank list.
 *
 * @author      Marcel Werk
 * @copyright   2001-2023 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.0
 *
 * @deprecated 6.2 use `ViewableUserRankList` instead
 *
 * @extends I18nDatabaseObjectList<UserRank>
 */
class I18nUserRankList extends I18nDatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $i18nFields = ['rankTitle' => 'rankTitleI18n'];

    /**
     * @inheritDoc
     */
    public $className = UserRank::class;
}
