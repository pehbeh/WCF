<?php

namespace wcf\data\captcha\question;

use wcf\data\DatabaseObjectList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Represents a list of captcha questions.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<CaptchaQuestion>
 */
class CaptchaQuestionList extends DatabaseObjectList
{
    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        if ($this->objectIDs !== []) {
            $this->loadContent();
        }
    }

    private function loadContent(): void
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add("questionID IN(?)", [$this->objectIDs]);

        $sql = "SELECT *
                FROM   wcf1_captcha_question_content
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        while ($row = $statement->fetchArray()) {
            $this->objects[$row['questionID']]->setContent($row['languageID'], $row['question'], $row['answers']);
        }
    }
}
