<?php

namespace wcf\event\gridView\admin;

use wcf\event\IPsr14Event;
use wcf\system\gridView\admin\PackageUpdateServerGridView;

/**
 * Indicates that the provider for package update server grid view has been initialized.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class PackageUpdateServerGridViewInitialized implements IPsr14Event
{
    public function __construct(public readonly PackageUpdateServerGridView $gridView)
    {
    }
}
