<?php

namespace wcf\system\cache\persistent;

use Symfony\Contracts\Cache\ItemInterface;
use wcf\data\core\object\CoreObjectList;
use wcf\system\SingletonFactory;

/**
 * Persistent cache implementation for core objects.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractPersistentCache<array<string, class-string<SingletonFactory>>>
 */
final class CoreObjectCache extends AbstractPersistentCache
{
    #[\Override]
    public function __invoke(ItemInterface $item): array
    {
        $coreObjectList = new CoreObjectList();
        $coreObjectList->readObjects();
        $coreObjects = $coreObjectList->getObjects();

        $data = [];
        foreach ($coreObjects as $coreObject) {
            $tmp = \explode('\\', $coreObject->objectName);
            $className = \array_pop($tmp);
            $data[$className] = $coreObject->objectName;
        }

        return $data;
    }
}
