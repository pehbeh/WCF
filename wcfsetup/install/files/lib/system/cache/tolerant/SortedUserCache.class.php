<?php

namespace wcf\system\cache\tolerant;

use wcf\data\condition\Condition;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\user\UserList;
use wcf\system\condition\IObjectListCondition;

/**
 * Caches a sorted list of userIDs.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractTolerantCache<list<int>>
 */
final class SortedUserCache extends AbstractTolerantCache
{
    public function __construct(
        public readonly string $sortField,
        public readonly string $sortOrder = 'DESC',
        public readonly int $limit = 5,
        public readonly bool $positiveValuesOnly = false,
        /** @var Condition[] */
        public readonly array $conditions = []
    ) {
    }

    #[\Override]
    public function getLifetime(): int
    {
        return 300;
    }

    #[\Override]
    protected function rebuildCacheData(): array
    {
        $userProfileList = new UserList();
        if ($this->positiveValuesOnly) {
            $userProfileList->getConditionBuilder()->add('user_table.' . $this->sortField . ' > ?', [0]);
        }

        foreach ($this->conditions as $condition) {
            /** @var IObjectListCondition<DatabaseObjectList<DatabaseObject>> $processor */
            $processor = $condition->getObjectType()->getProcessor();
            $processor->addObjectListCondition($userProfileList, $condition->conditionData);
        }

        $userProfileList->sqlOrderBy = 'user_table.' . $this->sortField . ' ' . $this->sortOrder;
        $userProfileList->sqlLimit = $this->limit;
        $userProfileList->readObjectIDs();

        return $userProfileList->getObjectIDs();
    }
}
