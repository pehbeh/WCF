<?php

use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

$sql = "SELECT questionID, question, answers
        FROM   wcf1_captcha_question";
$statement = WCF::getDB()->prepare($sql);
$statement->execute();

$questionIDs = $questions = $answers = [];
while ($row = $statement->fetchArray()) {
    $questionIDs[] = $row['questionID'];
    $questions[$row['questionID']] = $row['question'];
    $answers[$row['questionID']] = $row['answers'];
}

$sql = "INSERT INTO wcf1_captcha_question_content
                    (questionID, languageID, question, answers)
        VALUES      (?, ?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);

$languageItems = [];
foreach ($questionIDs as $questionID) {
    $answer = $answers[$questionID];
    $question = $questions[$questionID];
    $multilingual = false;

    if (\preg_match('~^wcf\.captcha\.question\.question\.question\d+$~', $question, $matches)) {
        $languageItems[] = $question;
        $multilingual = true;
    }
    if (\preg_match('~^wcf\.captcha\.question\.answers\.question\d+$~', $answer, $matches)) {
        $languageItems[] = $answer;
        $multilingual = true;
    }

    if ($multilingual) {
        foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
            $statement->execute([
                $questionID,
                $language->languageID,
                $language->get($question),
                $language->get($answer),
            ]);
        }
    } else {
        $statement->execute([
            $questionID,
            null,
            $question,
            $answer,
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
