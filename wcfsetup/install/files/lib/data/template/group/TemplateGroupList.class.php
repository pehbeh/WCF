<?php

namespace wcf\data\template\group;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of template groups.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<TemplateGroup>
 */
class TemplateGroupList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = TemplateGroup::class;
}
