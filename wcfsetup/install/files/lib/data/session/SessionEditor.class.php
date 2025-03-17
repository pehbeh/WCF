<?php

namespace wcf\data\session;

use wcf\data\acp\session\ACPSessionEditor;

/**
 * Provides functions to edit sessions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method Session getDecoratedObject()
 * @method static Session create(mixed[] $parameters = [])
 * @mixin Session
 */
class SessionEditor extends ACPSessionEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = Session::class;
}
