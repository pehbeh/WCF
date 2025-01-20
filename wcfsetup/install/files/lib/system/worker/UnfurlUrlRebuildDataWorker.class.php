<?php

namespace wcf\system\worker;

use wcf\data\unfurl\url\UnfurlUrl;
use wcf\data\unfurl\url\UnfurlUrlEditor;
use wcf\data\unfurl\url\UnfurlUrlList;
use wcf\system\WCF;

/**
 * Worker implementation for unfurl url rebuild data.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @method  UnfurlUrlList    getObjectList()
 */
final class UnfurlUrlRebuildDataWorker extends AbstractLinearRebuildDataWorker
{
    /**
     * @inheritDoc
     */
    protected $objectListClassName = UnfurlUrlList::class;

    /**
     * @inheritDoc
     */
    protected $limit = 10;

    #[\Override]
    public function execute()
    {
        parent::execute();

        if (\count($this->getObjectList()) === 0) {
            return;
        }

        $sql = "UPDATE wcf1_unfurl_url_image
                SET    isStored = ?,
                       fileID = ?
                WHERE  imageID = ?";
        $statement = WCF::getDB()->prepare($sql);

        foreach ($this->getObjectList() as $unfurlUrl) {
            if (!$unfurlUrl->imageID || !$unfurlUrl->isStored || $unfurlUrl->fileID !== null) {
                continue;
            }

            $fileLocation = \sprintf(
                '%s%s%s/%s.%s',
                WCF_DIR,
                UnfurlUrl::IMAGE_DIR,
                \substr($unfurlUrl->imageUrlHash, 0, 2),
                $unfurlUrl->imageUrlHash,
                $unfurlUrl->imageExtension
            );

            $file = UnfurlUrlEditor::createWebpThumbnail(
                $fileLocation,
                \pathinfo($unfurlUrl->imageUrl, PATHINFO_FILENAME)
            );

            $statement->execute([
                $file !== null ? 1 : 0,
                $file?->fileID,
                $unfurlUrl->imageID,
            ]);
        }
    }
}
