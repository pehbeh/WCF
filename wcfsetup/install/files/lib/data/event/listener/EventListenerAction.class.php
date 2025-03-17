<?php

namespace wcf\data\event\listener;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes event listener-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<EventListener, EventListenerEditor>
 */
class EventListenerAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = EventListenerEditor::class;
}
