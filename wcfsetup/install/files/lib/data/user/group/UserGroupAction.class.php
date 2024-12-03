<?php

namespace wcf\data\user\group;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes user group-related actions.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method  UserGroupEditor[]   getObjects()
 * @method  UserGroupEditor     getSingleObject()
 */
class UserGroupAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    public $className = UserGroupEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['admin.user.canAddGroup'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['admin.user.canDeleteGroup'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['admin.user.canEditGroup'];

    /**
     * @inheritDoc
     */
    protected $requireACP = ['create', 'delete', 'update'];

    /**
     * @inheritDoc
     * @return  UserGroup
     */
    public function create()
    {
        /** @var UserGroup $group */
        $group = parent::create();

        if (isset($this->parameters['options'])) {
            $groupEditor = new UserGroupEditor($group);
            $groupEditor->updateGroupOptions($this->parameters['options']);
        }

        return $group;
    }

    /**
     * @inheritDoc
     */
    public function update()
    {
        if (empty($this->objects)) {
            $this->readObjects();
        }

        foreach ($this->getObjects() as $object) {
            $object->update($this->parameters['data']);
            $object->updateGroupOptions($this->parameters['options']);
        }
    }
}
