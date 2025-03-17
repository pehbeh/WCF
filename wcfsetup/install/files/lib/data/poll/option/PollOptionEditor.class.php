<?php

namespace wcf\data\poll\option;

use wcf\data\DatabaseObjectEditor;

/**
 * Extends the poll option object with functions to create, update and delete poll options.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       PollOption
 * @extends DatabaseObjectEditor<PollOption>
 */
class PollOptionEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = PollOption::class;
}
