<?php

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

$sql = "SELECT bbcodeID, buttonLabel
        FROM   wcf1_bbcode
        WHERE  showButton = ?
           OR  buttonLabel <> ''";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([1]);
$buttonLabels = $statement->fetchMap('bbcodeID', 'buttonLabel');

$sql = "INSERT INTO wcf1_bbcode_content
                    (bbcodeID, languageID, buttonLabel)
        VALUES      (?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);

$languageItems = [];
foreach ($buttonLabels as $bbcodeID => $buttonLabel) {
    if (\preg_match('~^\w+(\.\w+){2,}$~', $buttonLabel, $matches)) {
        $languageItems[] = $buttonLabel;

        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $statement->execute([
                $bbcodeID,
                $language->languageID,
                $language->get($buttonLabel),
            ]);
        }
    } else {
        $statement->execute([
            $bbcodeID,
            LanguageFactory::getInstance()->getDefaultLanguageID(),
            $buttonLabel,
        ]);
    }
}

if ($languageItems !== []) {
    $conditionBuilder = new PreparedStatementConditionBuilder();
    $conditionBuilder->add('languageItem IN (?)', [$languageItems]);

    $sql = "DELETE FROM wcf1_language_item
            {$conditionBuilder}";
    $statement = WCF::getDB()->prepare($sql);
    $statement->execute($conditionBuilder->getParameters());
}
