<?php

namespace wcf\system\message\embedded\object;

use wcf\data\DatabaseObjectDecorator;
use wcf\data\object\type\ObjectType;

/**
 * Provides default implementations for message embedded object handlers.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin   ObjectType
 * @extends DatabaseObjectDecorator<ObjectType>
 */
abstract class AbstractMessageEmbeddedObjectHandler extends DatabaseObjectDecorator implements
    IMessageEmbeddedObjectHandler
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = ObjectType::class;
}
