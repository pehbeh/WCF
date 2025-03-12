<?php

namespace wcf\system\cache\builder;

use wcf\data\condition\Condition;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\user\UserList;
use wcf\system\condition\IObjectListCondition;

/**
 * Caches a list of the newest members.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
abstract class AbstractSortedUserCacheBuilder extends AbstractCacheBuilder
{
    /**
     * default limit value if no limit parameter is provided
     * @var int
     */
    protected $defaultLimit = 5;

    /**
     * default sort order if no sort order parameter is provided
     * @var string
     */
    protected $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    protected $maxLifetime = 300;

    /**
     * if `true`, only positive values of the database column will be considered
     * @var bool
     */
    protected $positiveValuesOnly = false;

    /**
     * database table column used for sorting
     * @var string
     */
    protected $sortField;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        $sortOrder = $this->defaultSortOrder;
        if (!empty($parameters['sortOrder'])) {
            $sortOrder = $parameters['sortOrder'];
        }

        $userProfileList = new UserList();
        if ($this->positiveValuesOnly) {
            $userProfileList->getConditionBuilder()->add('user_table.' . $this->sortField . ' > ?', [0]);
        }
        if (isset($parameters['conditions'])) {
            /** @var Condition $condition */
            foreach ($parameters['conditions'] as $condition) {
                /** @var IObjectListCondition<DatabaseObjectList<DatabaseObject>> $processor */
                $processor = $condition->getObjectType()->getProcessor();
                $processor->addObjectListCondition($userProfileList, $condition->conditionData);
            }
        }
        $userProfileList->sqlOrderBy = 'user_table.' . $this->sortField . ' ' . $sortOrder;
        $userProfileList->sqlLimit = !empty($parameters['limit']) ? $parameters['limit'] : $this->defaultLimit;
        $userProfileList->readObjectIDs();

        return $userProfileList->getObjectIDs();
    }
}
