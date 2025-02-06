<?php

namespace wcf\system\interaction\bulk\admin;

use wcf\acp\action\DeleteUserContentAction;
use wcf\data\user\UserProfile;
use wcf\data\user\UserProfileList;
use wcf\event\interaction\bulk\admin\UserBulkInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\bulk\AbstractBulkInteractionProvider;
use wcf\system\interaction\bulk\BulkDeleteInteraction;
use wcf\system\interaction\bulk\BulkFormBuilderDialogInteraction;
use wcf\system\interaction\bulk\BulkRpcInteraction;
use wcf\system\interaction\InteractionConfirmationType;
use wcf\system\WCF;

/**
 * Bulk interaction provider for users.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserBulkInteractions extends AbstractBulkInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new BulkRpcInteraction(
                "ban",
                "core/users/%s/ban",
                "wcf.acp.user.ban",
                InteractionConfirmationType::Custom,
                "wcf.acp.user.banUser.description",
                static fn(UserProfile $user) => !$user->banned && $user->canBan()
            ),
            new BulkRpcInteraction(
                "unban",
                "core/users/%s/unban",
                "wcf.acp.user.unban",
                InteractionConfirmationType::Custom,
                "wcf.acp.user.unbanUser.description",
                static fn(UserProfile $user) => $user->banned && $user->canBan()
            ),
            new BulkRpcInteraction(
                "confirm-email",
                "core/users/%s/confirm-email",
                "wcf.acp.user.action.confirmEmail",
                isAvailableCallback: static fn(UserProfile $user) => $user->canEnable() && !$user->isEmailConfirmed()
            ),
            new BulkRpcInteraction(
                "unconfirm-email",
                "core/users/%s/unconfirm-email",
                "wcf.acp.user.action.unconfirmEmail",
                isAvailableCallback: static fn(UserProfile $user) => $user->canEnable() && $user->isEmailConfirmed()
            ),
            new BulkRpcInteraction(
                "send-new-password",
                "core/users/%s/send-new-password",
                "wcf.acp.user.action.sendNewPassword",
                InteractionConfirmationType::Custom,
                "wcf.acp.user.action.sendNewPassword.confirmMessage",
                static fn(UserProfile $user) => WCF::getSession()->getPermission("admin.user.canEditPassword") &&
                    $user->userID !== WCF::getUser()->userID
            ),
            new BulkDeleteInteraction("core/users/%s", static fn(UserProfile $user) => $user->canDelete()),
            new BulkFormBuilderDialogInteraction(
                "delete-content",
                DeleteUserContentAction::class,
                "wcf.acp.content.removeContent",
                static fn(UserProfile $user) => $user->canDelete()
            ),
        ]);

        EventHandler::getInstance()->fire(
            new UserBulkInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectListClassName(): string
    {
        return UserProfileList::class;
    }
}
