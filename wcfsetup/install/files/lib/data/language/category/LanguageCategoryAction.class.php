<?php

namespace wcf\data\language\category;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes language category-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends AbstractDatabaseObjectAction<LanguageCategory, LanguageCategoryEditor>
 */
class LanguageCategoryAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = LanguageCategoryEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.language.canManageLanguage'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.language.canManageLanguage'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.language.canManageLanguage'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];
}
