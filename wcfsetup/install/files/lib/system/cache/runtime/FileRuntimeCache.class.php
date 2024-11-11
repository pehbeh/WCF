<?php

namespace wcf\system\cache\runtime;

use wcf\data\file\File;
use wcf\data\file\FileList;

/**
 * Runtime cache implementation for files with thumbnails.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @method  File[]      getCachedObjects()
 * @method  File|null   getObject($objectID)
 * @method  File[]      getObjects(array $objectIDs)
 */
class FileRuntimeCache extends AbstractRuntimeCache
{
    /**
     * @inheritDoc
     */
    protected $listClassName = FileList::class;

    #[\Override]
    protected function getObjectList()
    {
        $fileList = new FileList();
        $fileList->loadThumbnails = true;

        return $fileList;
    }
}
