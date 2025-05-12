<?php

namespace wcf\system\captcha\question\command;

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
        public readonly int $questionID,
        /** @var array<int, array{question: string, answers: string}> */
        public readonly array $contents,
    ) {
    }

    public function __invoke(): void
    {
        if ($this->contents === []) {
            return;
        }

        $this->deleteOldContent($this->questionID);
        $this->saveContent($this->questionID, $this->contents);
    }

    private function deleteOldContent(int $questionID): void
    {
        $sql = "DELETE FROM wcf1_captcha_question_content
                WHERE       questionID = ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$questionID]);
    }

    /**
     * @param array<int, array{question: string, answers: string}> $contents
     */
    private function saveContent(int $questionID, array $contents): void
    {
        $sql = "INSERT INTO wcf1_captcha_question_content
                            (questionID, languageID, question, answers)
                VALUES      (?, ?, ?, ?)";
        $statement = WCF::getDB()->prepare($sql);

        foreach ($contents as $languageID => $content) {
            $statement->execute([
                $questionID,
                $languageID ?: null,
                $content['question'],
                $content['answers'],
            ]);
        }
    }
}
