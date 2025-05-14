<?php

namespace wcf\data;

/**
 * Abstract class for a database object that uses collections.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @template TDatabaseObjectCollection of DatabaseObjectCollection
 */
abstract class CollectionDatabaseObject extends DatabaseObject
{
    /**
     * @var TDatabaseObjectCollection
     */
    protected DatabaseObjectCollection $collection;

    /**
     * @param TDatabaseObjectCollection $collection
     */
    public function setCollection(DatabaseObjectCollection $collection): void
    {
        $this->collection = $collection;
    }

    /**
     * @return TDatabaseObjectCollection
     */
    public function getCollection(): DatabaseObjectCollection
    {
        if (!isset($this->collection)) {
            $this->collection = new ($this->getCollectionClassName())([
                $this
            ]);
        }

        return $this->collection;
    }

    public function getCollectionClassName(): string
    {
        return static::class . 'Collection';
    }
}
