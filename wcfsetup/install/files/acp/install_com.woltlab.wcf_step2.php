<?php

use wcf\data\category\CategoryEditor;
use wcf\data\object\type\ObjectTypeCache;
use wcf\data\reaction\type\ReactionTypeEditor;
use wcf\data\user\rank\UserRankEditor;
use wcf\data\user\UserEditor;
use wcf\data\user\UserProfileAction;
use wcf\system\image\adapter\ImagickImageAdapter;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

// set default landing page
$sql = "UPDATE  wcf1_application
        SET     landingPageID = (
                    SELECT  pageID
                    FROM    wcf1_page
                    WHERE   identifier = ?
                )
        WHERE   packageID = ?";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    'com.woltlab.wcf.Dashboard',
    1,
]);

// install default user ranks
$sql = "INSERT INTO wcf1_user_rank_content
                    (rankID, languageID, title)
        VALUES      (?, ?, ?)";
$statement = WCF::getDB()->prepare($sql);

foreach ([
    [4, 0, ['de' => 'Administrator', 'en' => 'Administrator'], 'blue'],
    [5, 0, ['de' => 'Moderator', 'en' => 'Moderator'], 'blue'],
    [3, 0, ['de' => 'Anfänger', 'en' => 'Beginner'], ''],
    [3, 300, ['de' => 'Schüler', 'en' => 'Student'], ''],
    [3, 900, ['de' => 'Fortgeschrittener', 'en' => 'Intermediate'], ''],
    [3, 3000, ['de' => 'Profi', 'en' => 'Professional'], ''],
    [3, 9000, ['de' => 'Meister', 'en' => 'Master'], ''],
    [3, 15000, ['de' => 'Erleuchteter', 'en' => 'Enlightened'], ''],
] as [$groupID, $requiredPoints, $rankTitles, $cssClassName]) {
    $userRank = UserRankEditor::create([
        'groupID' => $groupID,
        'requiredPoints' => $requiredPoints,
        'cssClassName' => $cssClassName,
    ]);

    foreach (LanguageFactory::getInstance()->getLanguages() as $language) {
        if (!isset($rankTitles[$language->languageCode])) {
            continue;
        }

        $statement->execute([
            $userRank->rankID,
            $language->languageID,
            $rankTitles[$language->languageCode],
        ]);
    }
}

// update administrator user rank and user online marking
$editor = new UserEditor(WCF::getUser());
$action = new UserProfileAction([$editor], 'updateUserRank');
$action->executeAction();
$action = new UserProfileAction([$editor], 'updateUserOnlineMarking');
$action->executeAction();

// install default reactions
foreach ([
    [1, 1, 'like.svg'],
    [2, 2, 'thanks.svg'],
    [3, 3, 'haha.svg'],
    [4, 4, 'confused.svg'],
    [5, 5, 'sad.svg'],
] as [$reactionTypeID, $showOrder, $iconFile]) {
    ReactionTypeEditor::create([
        'reactionTypeID' => $reactionTypeID,
        'title' => "wcf.reactionType.title{$reactionTypeID}",
        'showOrder' => $showOrder,
        'iconFile' => $iconFile,
    ]);
}

// add default article category
CategoryEditor::create([
    'objectTypeID' => ObjectTypeCache::getInstance()
        ->getObjectTypeIDByName('com.woltlab.wcf.category', 'com.woltlab.wcf.article.category'),
    'title' => 'Default Category',
    'description' => '',
    'time' => TIME_NOW,
]);

// Configure dynamic option values
$sql = "UPDATE  wcf1_option
        SET     optionValue = ?
        WHERE   optionName = ?";
$statement = WCF::getDB()->prepare($sql);

if (
    ImagickImageAdapter::isSupported()
    && ImagickImageAdapter::supportsAnimatedGIFs(ImagickImageAdapter::getVersion())
    && ImagickImageAdapter::supportsWebp()
) {
    $statement->execute([
        'imagick',
        'image_adapter_type',
    ]);
}

$user = WCF::getUser();
$statement->execute([
    $user->username,
    'mail_from_name',
]);
$statement->execute([
    $user->email,
    'mail_from_address',
]);
$statement->execute([
    $user->email,
    'mail_admin_address',
]);
