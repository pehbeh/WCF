<?php

namespace wcf\event\interaction\admin;

use wcf\event\IPsr14Event;
use wcf\system\interaction\admin\NoticeInteractions;

/**
 * Indicates that the provider for notice interactions is collecting interactions.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class NoticeInteractionCollecting implements IPsr14Event
{
    public function __construct(public readonly NoticeInteractions $param)
    {
    }
}
