<?php

namespace wcf\data\option\category;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes option category-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<OptionCategory, OptionCategoryEditor>
 */
class OptionCategoryAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = OptionCategoryEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.configuration.canEditOption'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.configuration.canEditOption'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.configuration.canEditOption'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];
}
