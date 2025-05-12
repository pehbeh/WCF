<?php

namespace wcf\data\captcha\question;

use wcf\data\DatabaseObject;
use wcf\system\language\LanguageFactory;
use wcf\system\Regex;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a captcha question.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @property-read   int $questionID unique id of the captcha question
 * @property-read   int $isDisabled is `1` if the captcha question is disabled and thus not offered to answer, otherwise `0`
 */
class CaptchaQuestion extends DatabaseObject
{
    /**
     * @var array<int, array{question: string, answers: string}>
     */
    protected array $content;

    /**
     * Returns the question in the active user's language.
     *
     * @return  string
     * @since   5.2
     */
    public function getQuestion()
    {
        return $this->getContent()['question'];
    }

    /**
     * Returns true if the given user input is an answer to this question.
     *
     * @param string $answer
     * @return  bool
     */
    public function isAnswer($answer)
    {
        $this->loadContent();

        $answers = \explode("\n", StringUtil::unifyNewlines($this->getContent()['answers']));
        foreach ($answers as $__answer) {
            if (\mb_substr($__answer, 0, 1) == '~' && \mb_substr($__answer, -1, 1) == '~') {
                if (Regex::compile(\mb_substr($__answer, 1, \mb_strlen($__answer) - 2), Regex::CASE_INSENSITIVE)->match($answer)) {
                    return true;
                }

                continue;
            } elseif (\mb_strtolower($__answer) == \mb_strtolower($answer)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @since 6.2
     */
    protected function loadContent(): void
    {
        if (isset($this->content)) {
            return;
        }

        $sql = "SELECT languageID, question, answers
                FROM   wcf1_captcha_question_content
                WHERE  questionID = ?";

        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([$this->questionID]);

        $this->content = [];
        while ($row = $statement->fetchArray()) {
            $this->content[$row['languageID'] ?: 0] = [
                'question' => $row['question'],
                'answers' => $row['answers'],
            ];
        }
    }

    /**
     * @return array{question: string, answers: string}
     * @since 6.2
     */
    protected function getContent(): array
    {
        $this->loadContent();

        return $this->content[WCF::getLanguage()->languageID]
            ?? $this->content[LanguageFactory::getInstance()->getDefaultLanguageID()]
            ?? \reset($this->content);
    }

    /**
     * @since 6.2
     */
    public function setContent(?int $languageID, string $question, string $answers): void
    {
        if (!isset($this->content)) {
            $this->content = [];
        }

        $this->content[$languageID ?: 0] = [
            'question' => $question,
            'answers' => $answers,
        ];
    }
}
