<?php

namespace wcf\system\user\rank\command;

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
        public readonly int $rankID,
        /** @var array<int, array{title: string}> */
        public readonly array $content
    ) {
    }

    public function __invoke(): void
    {
        if ($this->content === []) {
            return;
        }

        WCF::getDB()->beginTransaction();

        $this->deleteOldContent($this->rankID);
        $this->saveContent($this->rankID, $this->content);

        WCF::getDB()->commitTransaction();
    }

    private function deleteOldContent(int $rankID): void
    {
        $sql = "DELETE FROM wcf1_user_rank_content
                WHERE       rankID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$rankID]);
    }

    /**
     * @param array<int, array{title: string}> $content
     */
    private function saveContent(int $rankID, array $content): void
    {
        $sql = "INSERT INTO wcf1_user_rank_content
                            (rankID, languageID, title)
                VALUES      (?, ?, ?)";
        $statement = WCF::getDB()->prepare($sql);

        foreach ($content as $languageID => $_content) {
            $statement->execute([$rankID, $languageID ?: null, $_content['title']]);
        }
    }
}
