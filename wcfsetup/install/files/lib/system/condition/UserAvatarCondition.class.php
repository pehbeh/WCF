<?php

namespace wcf\system\condition;

use wcf\data\condition\Condition;
use wcf\data\DatabaseObjectList;
use wcf\data\user\User;
use wcf\data\user\UserList;
use wcf\system\exception\InvalidObjectArgument;
use wcf\system\WCF;

/**
 * Condition implementation for the avatar of a user.
 *
 * @author  Matthias Schmidt
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class UserAvatarCondition extends AbstractSelectCondition implements
    IContentCondition,
    IObjectListCondition,
    IUserCondition
{
    use TObjectListUserCondition;

    /**
     * @inheritDoc
     */
    protected $fieldName = 'userAvatar';

    /**
     * @inheritDoc
     */
    protected $label = 'wcf.user.condition.avatar';

    /**
     * value of the "user has no avatar" option
     * @var int
     */
    const NO_AVATAR = 0;

    /**
     * value of the "user has a custom avatar" option
     * @var int
     */
    const AVATAR = 1;

    /**
     * @deprecated 6.0 This value is reserved for backwards compatibility with existing conditions.
     */
    const GRAVATAR = 2;

    /**
     * @inheritDoc
     */
    public function addObjectListCondition(DatabaseObjectList $objectList, array $conditionData)
    {
        if (!($objectList instanceof UserList)) {
            throw new InvalidObjectArgument($objectList, UserList::class, 'Object list');
        }

        switch ($conditionData['userAvatar']) {
            case self::NO_AVATAR:
                $objectList->getConditionBuilder()->add('user_table.avatarFileID IS NULL');
                break;

            case self::AVATAR:
                $objectList->getConditionBuilder()->add('user_table.avatarFileID IS NOT NULL');
                break;

            case self::GRAVATAR:
                $objectList->getConditionBuilder()->add('1 = 0');
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function checkUser(Condition $condition, User $user)
    {
        switch ($condition->userAvatar) {
            case self::NO_AVATAR:
                return !$user->avatarFileID;

            case self::AVATAR:
                return $user->avatarFileID !== null;

            case self::GRAVATAR:
                return false;
        }
    }

    /**
     * @inheritDoc
     */
    protected function getOptions()
    {
        return [
            self::NO_SELECTION_VALUE => 'wcf.global.noSelection',
            self::NO_AVATAR => 'wcf.user.condition.avatar.noAvatar',
            self::AVATAR => 'wcf.user.condition.avatar.avatar',
        ];
    }

    /**
     * @inheritDoc
     */
    public function showContent(Condition $condition)
    {
        if (!WCF::getUser()->userID) {
            return false;
        }

        return $this->checkUser($condition, WCF::getUser());
    }
}
