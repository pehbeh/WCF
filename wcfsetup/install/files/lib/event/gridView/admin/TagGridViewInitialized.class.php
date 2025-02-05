<?php

namespace wcf\event\gridView\admin;

use wcf\event\IPsr14Event;
use wcf\system\gridView\admin\TagGridView;

/**
 * Indicates that the tag grid view has been initialized.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class TagGridViewInitialized implements IPsr14Event
{
    public function __construct(public readonly TagGridView $gridView)
    {
    }
}
