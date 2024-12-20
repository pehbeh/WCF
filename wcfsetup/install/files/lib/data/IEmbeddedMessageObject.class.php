<?php

namespace wcf\data;

/**
 * Interface for embedded message objects.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
interface IEmbeddedMessageObject
{
    /**
     * Loads embedded objects for the given object type and object IDs.
     */
    public function loadEmbeddedObjects(): void;
}
