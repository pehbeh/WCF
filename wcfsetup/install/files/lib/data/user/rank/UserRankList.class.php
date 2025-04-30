<?php

namespace wcf\data\user\rank;

use wcf\data\MultilingualContentList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Represents a list of user ranks.
 *
 * @author Olaf Braun, Marcel Werk
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends MultilingualContentList<UserRank>
 */
class UserRankList extends MultilingualContentList
{
    public function __construct(?int $preferredLanguageID = null)
    {
        parent::__construct($preferredLanguageID);

        $alias = $this->getDatabaseTableAlias();
        $this->sqlJoins = $this->sqlConditionJoins = "
            LEFT JOIN wcf1_user_rank_content userRankContent
            ON        {$alias}.rankID = userRankContent.rankID
            AND       {$alias}.languageID = userRankContent.languageID";
        $this->sqlSelects = "userRankContent.title";
    }

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

    #[\Override]
    protected function createSubSelectQuery(int $preferredLanguageID, int $defaultLanguageID): string
    {
        $alias = $this->getDatabaseTableAlias();
        $tableName = $this->getDatabaseTableName();

        return <<<SQL
            (
                SELECT {$alias}.*, (
                    SELECT   languageID
                    FROM     wcf1_user_rank_content userRankContent
                    WHERE    userRankContent.rankID = {$alias}.rankID
                    ORDER BY CASE
                        WHEN languageID = {$preferredLanguageID} THEN -2
                        WHEN languageID = {$defaultLanguageID} THEN -1
                        ELSE languageID
                    END ASC
                    LIMIT 1
                ) AS   languageID
                FROM   {$tableName} {$alias}
            )
        SQL;
    }
}
