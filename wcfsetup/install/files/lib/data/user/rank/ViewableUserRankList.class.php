<?php

namespace wcf\data\user\rank;

use wcf\system\language\LanguageFactory;
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
     * @inheritDoc
     */
    public $decoratorClassName = ViewableUserRank::class;

    public readonly int $langaugeID;

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

    private function createSelectQuery(): string
    {
        $alias = $this->getDatabaseTableAlias();
        $tableName = $this->getDatabaseTableName();
        $preferredLanguageID = $this->langaugeID;
        $defaultLanguageID = LanguageFactory::getInstance()->getDefaultLanguageID();

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

    // TODO Do the following functions have to be copied for each implementation?
    #[\Override]
    public function countObjects()
    {
        $sql = "SELECT  COUNT(*)
                FROM    " . $this->createSelectQuery() . " " . $this->getDatabaseTableAlias() . "
                " . $this->sqlConditionJoins . "
                " . $this->getConditionBuilder();
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($this->getConditionBuilder()->getParameters());

        return $statement->fetchSingleColumn();
    }

    #[\Override]
    public function readObjectIDs()
    {
        $this->objectIDs = [];
        $sql = "SELECT  " . $this->getDatabaseTableAlias() . "." . $this->getDatabaseTableIndexName() . " AS objectID
                FROM    " . $this->createSelectQuery() . " " . $this->getDatabaseTableAlias() . "
                " . $this->sqlConditionJoins . "
                " . $this->getConditionBuilder() . "
                " . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
        $statement = WCF::getDB()->prepare($sql, $this->sqlLimit, $this->sqlOffset);
        $statement->execute($this->getConditionBuilder()->getParameters());
        $this->objectIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
    }

    #[\Override]
    public function readObjects()
    {
        if ($this->objectIDs !== null) {
            if (empty($this->objectIDs)) {
                return;
            }

            $objectIdPlaceholder = "?" . \str_repeat(',?', \count($this->objectIDs) - 1);

            $sql = "SELECT  " . (!empty($this->sqlSelects) ? $this->sqlSelects . ($this->useQualifiedShorthand ? ',' : '') : '') . "
                            " . ($this->useQualifiedShorthand ? $this->getDatabaseTableAlias() . '.*' : '') . "
                    FROM    " . $this->createSelectQuery() . " " . $this->getDatabaseTableAlias() . "
                            " . $this->sqlJoins . "
                    WHERE   " . $this->getDatabaseTableAlias() . "." . $this->getDatabaseTableIndexName() . " IN ({$objectIdPlaceholder})
                            " . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute($this->objectIDs);
            // @phpstan-ignore argument.templateType
            $this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));
        } else {
            $sql = "SELECT  " . (!empty($this->sqlSelects) ? $this->sqlSelects . ($this->useQualifiedShorthand ? ',' : '') : '') . "
                            " . ($this->useQualifiedShorthand ? $this->getDatabaseTableAlias() . '.*' : '') . "
                    FROM    " . $this->createSelectQuery() . " " . $this->getDatabaseTableAlias() . "
                    " . $this->sqlJoins . "
                    " . $this->getConditionBuilder() . "
                    " . (!empty($this->sqlOrderBy) ? "ORDER BY " . $this->sqlOrderBy : '');
            $statement = WCF::getDB()->prepare($sql, $this->sqlLimit, $this->sqlOffset);
            $statement->execute($this->getConditionBuilder()->getParameters());
            // @phpstan-ignore argument.templateType
            $this->objects = $statement->fetchObjects(($this->objectClassName ?: $this->className));
        }

        // decorate objects
        if (!empty($this->decoratorClassName)) {
            foreach ($this->objects as &$object) {
                $object = new $this->decoratorClassName($object);
            }
            unset($object);
        }

        // use table index as array index
        $objects = $this->indexToObject = [];
        foreach ($this->objects as $object) {
            $objectID = $object->getObjectID();
            $objects[$objectID] = $object;

            $this->indexToObject[] = $objectID;
        }
        $this->objectIDs = $this->indexToObject;
        $this->objects = $objects;
    }
}
