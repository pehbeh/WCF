<?php

namespace wcf\system\cache\runtime;

use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObjectList;
use wcf\system\SingletonFactory;

/**
 * Abstract implementation of a runtime cache.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 * @template T of DatabaseObject|DatabaseObjectDecorator
 * @implements IRuntimeCache<T>
 */
abstract class AbstractRuntimeCache extends SingletonFactory implements IRuntimeCache
{
    /**
     * name of the DatabaseObjectList class
     * @var string
     */
    protected $listClassName = '';

    /**
     * ids of objects which will be fetched next
     * @var int[]
     */
    protected $objectIDs = [];

    /**
     * cached DatabaseObject objects
     * @var T[]
     */
    protected $objects = [];

    #[\Override]
    public function cacheObjectID($objectID)
    {
        $this->cacheObjectIDs([$objectID]);
    }

    #[\Override]
    public function cacheObjectIDs(array $objectIDs)
    {
        foreach ($objectIDs as $objectID) {
            if (!\array_key_exists($objectID, $this->objects) && !isset($this->objectIDs[$objectID])) {
                $this->objectIDs[$objectID] = $objectID;
            }
        }
    }

    /**
     * Fetches the objects for the pending object ids.
     *
     * @return void
     */
    protected function fetchObjects()
    {
        $objectList = $this->getObjectList();
        $objectList->setObjectIDs(\array_values($this->objectIDs));
        $objectList->readObjects();
        $this->objects += $objectList->getObjects();

        // create null entries for non-existing objects
        foreach ($this->objectIDs as $objectID) {
            if (!\array_key_exists($objectID, $this->objects)) {
                $this->objects[$objectID] = null;
            }
        }

        $this->objectIDs = [];
    }

    #[\Override]
    public function getCachedObjects()
    {
        return $this->objects;
    }

    #[\Override]
    public function getObject($objectID)
    {
        if (\array_key_exists($objectID, $this->objects)) {
            return $this->objects[$objectID];
        }

        $this->cacheObjectID($objectID);

        $this->fetchObjects();

        return $this->objects[$objectID];
    }

    /**
     * Returns a database object list object to fetch cached objects.
     *
     * @return DatabaseObjectList
     */
    protected function getObjectList()
    {
        return new $this->listClassName();
    }

    #[\Override]
    public function getObjects(array $objectIDs)
    {
        $objects = [];

        // set already cached objects
        foreach ($objectIDs as $key => $objectID) {
            if (\array_key_exists($objectID, $this->objects)) {
                $objects[$objectID] = $this->objects[$objectID];
                unset($objectIDs[$key]);
            }
        }

        if (!empty($objectIDs)) {
            $this->cacheObjectIDs($objectIDs);

            $this->fetchObjects();

            // set newly loaded cached objects
            foreach ($objectIDs as $objectID) {
                $objects[$objectID] = $this->objects[$objectID];
            }
        }

        return $objects;
    }

    #[\Override]
    public function removeObject($objectID)
    {
        $this->removeObjects([$objectID]);
    }

    #[\Override]
    public function removeObjects(array $objectIDs)
    {
        foreach ($objectIDs as $objectID) {
            unset($this->objects[$objectID]);
        }
    }
}
