<?php

namespace wcf\data\event\listener;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of event listener.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<EventListener>
 */
class EventListenerList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = EventListener::class;
}
