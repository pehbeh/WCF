<?php

namespace wcf\event\gridView;

use wcf\event\IPsr14Event;
use wcf\system\view\grid\CronjobLogGridView;

/**
 * Indicates that the cronjob log grid view has been initialized.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class CronjobLogGridViewInitialized implements IPsr14Event
{
    public function __construct(public readonly CronjobLogGridView $gridView) {}
}
