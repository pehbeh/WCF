<?php

namespace wcf\data\user\rank;

use wcf\data\DatabaseObjectList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Represents a list of user ranks.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<UserRank>
 */
class UserRankList extends DatabaseObjectList
{
    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        if ($this->objectIDs !== []) {
            $this->loadRankTitles();
        }
    }

    private function loadRankTitles(): void
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add("rankID IN(?)", [$this->objectIDs]);

        $sql = "SELECT *
                FROM   wcf1_user_rank_content
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        while ($row = $statement->fetchArray()) {
            $this->objects[$row['rankID']]->setRankTitle($row['languageID'], $row['title']);
        }
    }
}
