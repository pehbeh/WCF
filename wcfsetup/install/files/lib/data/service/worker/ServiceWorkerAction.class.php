<?php

namespace wcf\data\service\worker;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.1
 *
 * @extends AbstractDatabaseObjectAction<ServiceWorker, ServiceWorkerEditor>
 */
class ServiceWorkerAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ServiceWorkerEditor::class;
}
