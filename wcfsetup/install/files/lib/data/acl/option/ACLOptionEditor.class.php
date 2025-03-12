<?php

namespace wcf\data\acl\option;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit acl options.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @mixin       ACLOption
 * @extends DatabaseObjectEditor<ACLOption>
 */
class ACLOptionEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    public static $baseClass = ACLOption::class;
}
