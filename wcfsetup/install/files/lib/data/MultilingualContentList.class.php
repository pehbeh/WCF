<?php

namespace wcf\data;

use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @template-covariant TDatabaseObject of DatabaseObject|DatabaseObjectDecorator<DatabaseObject>
 * @extends DatabaseObjectList<TDatabaseObject>
 */
abstract class MultilingualContentList extends DatabaseObjectList
{
    public readonly int $preferredLanguageID;

    public function __construct(?int $preferredLanguageID = null)
    {
        parent::__construct();

        if ($preferredLanguageID === null) {
            $preferredLanguageID = WCF::getLanguage()->languageID;
        }
        $this->preferredLanguageID = $preferredLanguageID;
    }

    #[\Override]
    public function countObjects()
    {
        $sql = "SELECT  COUNT(*)
                FROM    " . $this->getSubSelectQuery() . " " . $this->getDatabaseTableAlias() . "
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
                FROM    " . $this->getSubSelectQuery() . " " . $this->getDatabaseTableAlias() . "
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
                    FROM    " . $this->getSubSelectQuery() . " " . $this->getDatabaseTableAlias() . "
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
                    FROM    " . $this->getSubSelectQuery() . " " . $this->getDatabaseTableAlias() . "
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

    protected function getSubSelectQuery(): string
    {
        return $this->createSubSelectQuery(
            $this->preferredLanguageID,
            LanguageFactory::getInstance()->getDefaultLanguageID()
        );
    }

    abstract protected function createSubSelectQuery(int $preferredLanguageID, int $defaultLanguageID): string;
}
