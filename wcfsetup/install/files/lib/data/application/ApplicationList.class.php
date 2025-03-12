<?php

namespace wcf\data\application;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of applications.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @template TDatabaseObject of Application|DatabaseObjectDecorator<Application> = Application
 * @extends DatabaseObjectList<TDatabaseObject>
 */
class ApplicationList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Application::class;
}
