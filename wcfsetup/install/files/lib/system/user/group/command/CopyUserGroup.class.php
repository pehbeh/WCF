<?php

namespace wcf\system\user\group\command;

use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupAction;
use wcf\data\user\group\UserGroupEditor;
use wcf\system\cache\CacheHandler;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Copies a user group.
 *
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
final class CopyUserGroup
{
    public function __construct(
        public readonly UserGroup $userGroup,
        public readonly bool $copyUserGroupOptions,
        public readonly bool $copyMembers,
        public readonly bool $copyACLOptions
    ) {
    }

    public function __invoke()
    {
        // fetch user group option values
        if ($this->copyUserGroupOptions) {
            $sql = "SELECT  optionID, optionValue
                    FROM    wcf1_user_group_option_value
                    WHERE   groupID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->userGroup->groupID]);
        } else {
            $sql = "SELECT  optionID, defaultValue AS optionValue
                    FROM    wcf1_user_group_option";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute();
        }

        $optionValues = $statement->fetchMap('optionID', 'optionValue');

        $groupType = $this->userGroup->groupType;
        // When copying special user groups of which only one may exist,
        // change the group type to 'other'.
        if (\in_array($groupType, [UserGroup::EVERYONE, UserGroup::GUESTS, UserGroup::USERS, UserGroup::OWNER])) {
            $groupType = UserGroup::OTHER;
        }

        /** @var UserGroup $group */
        $group = (new UserGroupAction([], 'create', [
            'data' => [
                'groupName' => $this->userGroup->groupName,
                'groupDescription' => $this->userGroup->groupDescription,
                'priority' => $this->userGroup->priority,
                'userOnlineMarking' => $this->userGroup->userOnlineMarking,
                'showOnTeamPage' => $this->userGroup->showOnTeamPage,
                'groupType' => $groupType,
            ],
            'options' => $optionValues,
        ]))->executeAction()['returnValues'];
        $groupEditor = new UserGroupEditor($group);

        // update group name
        $groupName = $this->userGroup->groupName;
        if (\preg_match('~^wcf\.acp\.group\.group\d+$~', $this->userGroup->groupName)) {
            $groupName = 'wcf.acp.group.group' . $group->groupID;

            // create group name language item
            $sql = "INSERT INTO wcf1_language_item
                                (languageID, languageItem, languageItemValue, languageItemOriginIsSystem, languageCategoryID, packageID)
                    SELECT      languageID, '" . $groupName . "', CONCAT(languageItemValue, ' (2)'), 0, languageCategoryID, packageID
                    FROM        wcf1_language_item
                    WHERE       languageItem = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->userGroup->groupName]);
        } else {
            $groupName .= ' (2)';
        }

        // update group name
        $groupDescription = $this->userGroup->groupName;
        if (\preg_match('~^wcf\.acp\.group\.groupDescription\d+$~', $this->userGroup->groupDescription)) {
            $groupDescription = 'wcf.acp.group.groupDescription' . $group->groupID;

            // create group name language item
            $sql = "INSERT INTO wcf1_language_item
                                (languageID, languageItem, languageItemValue, languageItemOriginIsSystem, languageCategoryID, packageID)
                    SELECT      languageID, '" . $groupDescription . "', languageItemValue, 0, languageCategoryID, packageID
                    FROM        wcf1_language_item
                    WHERE       languageItem = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->userGroup->groupDescription]);
        }

        $groupEditor->update([
            'groupDescription' => $groupDescription,
            'groupName' => $groupName,
        ]);

        // copy members
        if ($this->copyMembers) {
            $sql = "INSERT INTO wcf1_user_to_group
                                (userID, groupID)
                    SELECT      userID, " . $group->groupID . "
                    FROM        wcf1_user_to_group
                    WHERE       groupID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->userGroup->groupID]);
        }

        // copy acl options
        if ($this->copyACLOptions) {
            $sql = "INSERT INTO wcf1_acl_option_to_group
                                (optionID, objectID, groupID, optionValue)
                    SELECT      optionID, objectID, " . $group->groupID . ", optionValue
                    FROM        wcf1_acl_option_to_group
                    WHERE       groupID = ?";
            $statement = WCF::getDB()->prepare($sql);
            $statement->execute([$this->userGroup->groupID]);

            // it is likely that applications or plugins use caches
            // for acl option values like for the labels which have
            // to be renewed after copying the acl options; because
            // there is no other way to delete these caches, we simply
            // delete all caches
            CacheHandler::getInstance()->flushAll();
        }

        // reset language cache
        LanguageFactory::getInstance()->deleteLanguageCache();

        UserGroupEditor::resetCache();

        return $group;
    }
}
