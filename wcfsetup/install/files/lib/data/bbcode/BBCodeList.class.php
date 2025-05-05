<?php

namespace wcf\data\bbcode;

use wcf\data\MultilingualContentList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Represents a list of bbcodes.
 *
 * @author Olaf Braun, Alexander Ebert
 * @copyright   2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends MultilingualContentList<BBCode>
 */
class BBCodeList extends MultilingualContentList
{
    /**
     * @inheritDoc
     */
    public $className = BBCode::class;

    public function __construct(?int $preferredLanguageID = null)
    {
        parent::__construct($preferredLanguageID);

        $alias = $this->getDatabaseTableAlias();
        $this->sqlJoins = $this->sqlConditionJoins = "
            LEFT JOIN wcf1_bbcode_content bbCodeContent
            ON        {$alias}.bbcodeID = bbCodeContent.bbcodeID
            AND       {$alias}.languageID = bbCodeContent.languageID";
        $this->sqlSelects = "bbCodeContent.buttonLabel";
    }

    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        if ($this->objectIDs !== []) {
            $this->loadButtonLabels();
        }
    }

    private function loadButtonLabels(): void
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add("bbcodeID IN(?)", [$this->objectIDs]);

        $sql = "SELECT *
                FROM   wcf1_bbcode_content
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        while ($row = $statement->fetchArray()) {
            $this->objects[$row['bbcodeID']]->setButtonLabel($row['languageID'], $row['buttonLabel']);
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
                    FROM     wcf1_bbcode_content bbCodeContent
                    WHERE    bbCodeContent.bbcodeID = {$alias}.bbcodeID
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
