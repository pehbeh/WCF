<?php

namespace wcf\data\package\installation\queue;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of package installation queues.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<PackageInstallationQueue>
 */
class PackageInstallationQueueList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = PackageInstallationQueue::class;
}
