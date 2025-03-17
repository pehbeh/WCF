<?php

namespace wcf\system\event;

/**
 * *DEPRECATED*
 * EventListeners can be registered for a specific event in many controller objects.
 *
 * @deprecated  2.1, use \wcf\system\event\listener\IParameterizedEventListener
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
interface IEventListener
{
    /**
     * Executes this action.
     *
     * @param mixed $eventObj
     * @param string $className
     * @param string $eventName
     * @return void
     */
    public function execute($eventObj, $className, $eventName);
}
