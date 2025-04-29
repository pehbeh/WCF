<?php

namespace wcf\data\user\rank;

use wcf\data\TMultilingualContentList;
use wcf\system\WCF;

/**
 * @author Olaf Braun
 * @copyright 2001-2026 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends UserRankList<ViewableUserRank>
 */
class ViewableUserRankList extends UserRankList
{
    /**
     * @use TMultilingualContentList<ViewableUserRank>
     */
    use TMultilingualContentList;

    /**
     * @inheritDoc
     */
    public $decoratorClassName = ViewableUserRank::class;

    public function __construct(?int $langaugeID = null)
    {
        parent::__construct();

        if ($langaugeID === null) {
            $langaugeID = WCF::getLanguage()->languageID;
        }
        $this->langaugeID = $langaugeID;

        $alias = $this->getDatabaseTableAlias();
        $this->sqlJoins = $this->sqlConditionJoins = "
            LEFT JOIN wcf1_user_rank_content userRankContent
            ON        {$alias}.rankID = userRankContent.rankID
            AND       (({$alias}.languageID IS NULL AND userRankContent.languageID IS NULL) OR ({$alias}.languageID = userRankContent.languageID))";
        $this->sqlSelects = "userRankContent.title";
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
