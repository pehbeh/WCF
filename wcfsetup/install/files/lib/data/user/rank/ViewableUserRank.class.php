<?php

namespace wcf\data\user\rank;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\ITitledObject;
use wcf\system\WCF;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property-read ?string $title
 * @property-read ?int $languageID
 * @mixin   UserRank
 * @extends DatabaseObjectDecorator<UserRank>
 */
class ViewableUserRank extends DatabaseObjectDecorator implements ITitledObject
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = UserRank::class;

    #[\Override]
    public function getTitle(): string
    {
        // For backward compatibility, titles may not yet have been migrated to `wcf1_user_rank_content` and may therefore be `null`.
        return $this->title ?? WCF::getLanguage()->get($this->rankTitle);
    }
}
