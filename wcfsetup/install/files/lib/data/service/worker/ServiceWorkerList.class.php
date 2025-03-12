<?php

namespace wcf\data\service\worker;

use wcf\data\DatabaseObjectList;

/**
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 *
 * @extends DatabaseObjectList<ServiceWorker>
 */
class ServiceWorkerList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ServiceWorker::class;
}
