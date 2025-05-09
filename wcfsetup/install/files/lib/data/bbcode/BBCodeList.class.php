<?php

namespace wcf\data\bbcode;

use wcf\data\DatabaseObjectList;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\WCF;

/**
 * Represents a list of bbcodes.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 *
 * @extends DatabaseObjectList<BBCode>
 */
class BBCodeList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = BBCode::class;

    #[\Override]
    public function readObjects()
    {
        parent::readObjects();

        if ($this->objectIDs !== []) {
            $this->loadButtonLabels();
        }
    }

    private function loadButtonLabels(): void
    {
        $conditionBuilder = new PreparedStatementConditionBuilder();
        $conditionBuilder->add("bbcodeID IN(?)", [$this->objectIDs]);

        $sql = "SELECT *
                FROM   wcf1_bbcode_content
                {$conditionBuilder}";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute($conditionBuilder->getParameters());

        while ($row = $statement->fetchArray()) {
            $this->objects[$row['bbcodeID']]->setButtonLabel($row['languageID'], $row['buttonLabel']);
        }
    }
}
