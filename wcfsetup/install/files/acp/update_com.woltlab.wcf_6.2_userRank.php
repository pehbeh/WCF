<?php

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

$sql = "SELECT rankID, rankTitle
        FROM   wcf1_user_rank";
$statement = WCF::getDB()->prepare($sql);
$statement->execute();
$titles = $statement->fetchMap('rankID', 'rankTitle');

$sql = "INSERT INTO wcf1_user_rank_content
                    (rankID, languageID, title)
        VALUES      (?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);

$languageItems = [];
foreach ($titles as $rankID => $title) {
    if (\preg_match('~^wcf\.user\.rank\.\w+$~', $title, $matches)) {
        $languageItems[] = $title;

        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $statement->execute([
                $rankID,
                $language->languageID,
                $language->get($title),
            ]);
        }
    } else {
        $statement->execute([
            $rankID,
            LanguageFactory::getInstance()->getDefaultLanguageID(),
            $title,
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
