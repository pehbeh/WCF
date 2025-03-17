<?php

namespace wcf\data\acp\template;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of ACP templates.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<ACPTemplate>
 */
class ACPTemplateList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ACPTemplate::class;
}
