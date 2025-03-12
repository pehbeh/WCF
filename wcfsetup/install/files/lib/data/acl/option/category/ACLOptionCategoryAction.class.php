<?php

namespace wcf\data\acl\option\category;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes acl option category-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<ACLOptionCategory, ACLOptionCategoryEditor>
 */
class ACLOptionCategoryAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ACLOptionCategoryEditor::class;
}
