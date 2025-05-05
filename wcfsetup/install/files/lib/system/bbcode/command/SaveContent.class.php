<?php

namespace wcf\system\bbcode\command;

use wcf\system\WCF;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
final class SaveContent
{
    public function __construct(
        public readonly int $bbcodeID,
        /** @var array<int, string> */
        public readonly array $buttonLabels,
    ) {
    }

    public function __invoke(): void
    {
        if ($this->buttonLabels === []) {
            return;
        }

        WCF::getDB()->beginTransaction();

        $this->deleteOldContent($this->bbcodeID);
        $this->saveContent($this->bbcodeID, $this->buttonLabels);

        WCF::getDB()->commitTransaction();
    }

    private function deleteOldContent(int $bbcodeID): void
    {
        $sql = "DELETE FROM wcf1_bbcode_content
                WHERE       bbcodeID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$bbcodeID]);
    }

    /**
     * @param array<int, string> $buttonLabels
     */
    private function saveContent(int $bbcodeID, array $buttonLabels): void
    {
        $sql = "INSERT INTO wcf1_bbcode_content
                            (bbcodeID, languageID, buttonLabel)
                VALUES      (?, ?, ?)";
        $statement = WCF::getDB()->prepare($sql);

        foreach ($buttonLabels as $languageID => $buttonLabel) {
            $statement->execute([$bbcodeID, $languageID, $buttonLabel]);
        }
    }
}
