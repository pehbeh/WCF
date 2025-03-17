<?php

namespace wcf\system\bbcode;

use wcf\data\bbcode\BBCode;
use wcf\data\DatabaseObjectDecorator;

/**
 * Provides an abstract implementation for bbcodes.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin   BBCode
 * @extends DatabaseObjectDecorator<BBCode>
 */
abstract class AbstractBBCode extends DatabaseObjectDecorator implements IBBCode
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = BBCode::class;
}
