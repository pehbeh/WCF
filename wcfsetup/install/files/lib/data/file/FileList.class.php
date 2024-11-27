<?php

namespace wcf\data\file;

use wcf\data\DatabaseObjectList;
use wcf\data\file\thumbnail\FileThumbnailList;

/**
 * @author Alexander Ebert
 * @copyright 2001-2023 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method File current()
 * @method File[] getObjects()
 * @method File|null getSingleObject()
 * @method File|null search($objectID)
 * @property File[] $objects
 */
class FileList extends DatabaseObjectList
{
    public $className = File::class;
    public bool $loadThumbnails = false;

    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        $this->loadThumbnails();
    }

    public function loadThumbnails(): void
    {
        if (!$this->loadThumbnails || $this->getObjectIDs() === []) {
            return;
        }

        $thumbnailList = new FileThumbnailList();
        $thumbnailList->getConditionBuilder()->add("fileID IN (?)", [$this->getObjectIDs()]);
        $thumbnailList->readObjects();
        foreach ($thumbnailList as $thumbnail) {
            $this->objects[$thumbnail->fileID]->addThumbnail($thumbnail);
        }
    }
}
