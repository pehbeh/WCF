<?php

namespace wcf\system\file\processor;

use wcf\data\file\File;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * File processor for unfurl url images.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UnfurlUrlImageFileProcessor extends AbstractFileProcessor
{

    #[\Override]
    public function acceptUpload(string $filename, int $fileSize, array $context): FileProcessorPreflightResult
    {
        return FileProcessorPreflightResult::InvalidContext;
    }

    #[\Override]
    public function canAdopt(File $file, array $context): bool
    {
        return false;
    }

    #[\Override]
    public function adopt(File $file, array $context): void
    {
        // do nothing
    }

    #[\Override]
    public function canDelete(File $file): bool
    {
        return false;
    }

    #[\Override]
    public function canDownload(File $file): bool
    {
        return true;
    }

    #[\Override]
    public function delete(array $fileIDs, array $thumbnailIDs): void
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add('fileID IN (?)', [$fileIDs]);

        $sql = "UPDATE wcf1_unfurl_url_image
                SET    isStored = ?
                " . $conditionBuilder;
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([0, ...$conditionBuilder->getParameters()]);
    }

    #[\Override]
    public function getObjectTypeName(): string
    {
        return 'com.woltlab.wcf.unfurl';
    }
}
