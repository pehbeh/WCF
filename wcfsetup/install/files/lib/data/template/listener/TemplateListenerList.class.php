<?php

namespace wcf\data\template\listener;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of template listener.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<TemplateListener>
 */
class TemplateListenerList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = TemplateListener::class;
}
