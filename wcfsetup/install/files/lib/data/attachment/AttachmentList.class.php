<?php

namespace wcf\data\attachment;

use wcf\data\DatabaseObjectList;
use wcf\system\cache\runtime\FileRuntimeCache;

/**
 * Represents a list of attachments.
 *
 * @author  Marcel Werk
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @method  Attachment      current()
 * @method  Attachment[]        getObjects()
 * @method  Attachment|null     getSingleObject()
 * @method  Attachment|null     search($objectID)
 * @property    Attachment[] $objects
 */
class AttachmentList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Attachment::class;

    public $enableFileLoading = true;

    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        if ($this->enableFileLoading) {
            $this->loadFiles();
        }
    }

    private function loadFiles(): void
    {
        $fileIDs = [];
        foreach ($this->objects as $attachment) {
            if ($attachment->fileID) {
                $fileIDs[] = $attachment->fileID;
            }
        }

        if ($fileIDs === []) {
            return;
        }

        FileRuntimeCache::getInstance()->cacheObjectIDs($fileIDs);

        foreach ($this->objects as $attachment) {
            $file = FileRuntimeCache::getInstance()->getObject($attachment->fileID);
            if ($file !== null) {
                $attachment->setFile($file);
            }
        }
    }
}
