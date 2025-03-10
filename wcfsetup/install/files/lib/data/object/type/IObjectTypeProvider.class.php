<?php

namespace wcf\data\object\type;

use wcf\data\DatabaseObject;

/**
 * Any object type provider should implement this interface.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @template TDatabaseObject of DatabaseObject
 */
interface IObjectTypeProvider
{
    /**
     * Returns an object by its ID.
     *
     * @param int $objectID
     * @return TDatabaseObject
     */
    public function getObjectByID($objectID);

    /**
     * Returns objects by their IDs.
     *
     * @param int[] $objectIDs
     * @return TDatabaseObject[]
     */
    public function getObjectsByIDs(array $objectIDs);
}
