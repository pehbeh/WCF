<?php

namespace wcf\system\worker;

use wcf\data\DatabaseObjectList;
use wcf\system\event\EventHandler;
use wcf\system\exception\ParentClassException;
use wcf\system\exception\SystemException;
use wcf\system\search\SearchIndexManager;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Abstract implementation of a linear rebuild data worker that will try to
 * process all objects starting with the id `1` up to the highest id.
 *
 * This differs from the previous rebuild data workers which use `LIMIT` and
 * `OFFSET` which scales poorly with large amounts of rows. In contrast this
 * worker will iterate over any possible id, causing some iterations to process
 * less objects than requested by the limit.
 *
 * @author Alexander Ebert
 * @copyright 2001-2024 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.1
 *
 * @template T of DatabaseObjectList
 * @extends AbstractRebuildDataWorker<T>
 */
abstract class AbstractLinearRebuildDataWorker extends AbstractRebuildDataWorker
{
    #[\Override]
    public function countObjects()
    {
        if ($this->count !== null) {
            return;
        }

        if ($this->objectList === null) {
            $this->initObjectList();
        }

        $sql = \sprintf(
            "SELECT MAX(%s) FROM %s",
            $this->objectList->getDatabaseTableIndexName(),
            $this->objectList->getDatabaseTableName(),
        );
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute();

        $this->count = $statement->fetchSingleColumn() ?: 0;
    }

    #[\Override]
    public function execute()
    {
        $this->objectList->readObjects();

        if (\count($this->objectList) === 0) {
            return;
        }

        SearchIndexManager::getInstance()->beginBulkOperation();

        EventHandler::getInstance()->fireAction($this, 'execute');
    }

    #[\Override]
    protected function initObjectList()
    {
        if (empty($this->objectListClassName)) {
            throw new SystemException('DatabaseObjectList class name not specified.');
        }

        if (!\is_subclass_of($this->objectListClassName, DatabaseObjectList::class)) {
            throw new ParentClassException($this->objectListClassName, DatabaseObjectList::class);
        }

        $this->objectList = new $this->objectListClassName();

        $indexName = \sprintf(
            '%s.%s',
            $this->objectList->getDatabaseTableAlias(),
            $this->objectList->getDatabaseTableIndexName(),
        );

        $this->objectList->getConditionBuilder()->add(
            "{$indexName} BETWEEN ? AND ?",
            [
                $this->limit * $this->loopCount + 1,
                $this->limit * $this->loopCount + $this->limit,
            ]
        );
        $this->objectList->sqlOrderBy = $indexName;
    }

    #[\Override]
    public function finalize()
    {
        if (\count($this->objectList) === 0) {
            return;
        }

        SearchIndexManager::getInstance()->commitBulkOperation();
        UserStorageHandler::getInstance()->shutdown();
    }
}
