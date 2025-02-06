<?php

namespace wcf\system\interaction\admin;

use wcf\acp\action\BanUserAction;
use wcf\acp\action\DeleteUserContentAction;
use wcf\acp\form\UserMailForm;
use wcf\data\user\UserProfile;
use wcf\event\interaction\admin\UserInteractionCollecting;
use wcf\system\event\EventHandler;
use wcf\system\interaction\AbstractInteractionProvider;
use wcf\system\interaction\DeleteInteraction;
use wcf\system\interaction\Divider;
use wcf\system\interaction\FormBuilderDialogInteraction;
use wcf\system\interaction\InteractionConfirmationType;
use wcf\system\interaction\LinkInteraction;
use wcf\system\interaction\RpcInteraction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Interaction provider for user interactions.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserInteractions extends AbstractInteractionProvider
{
    public function __construct()
    {
        $this->addInteractions([
            new FormBuilderDialogInteraction(
                "ban",
                LinkHandler::getInstance()->getControllerLink(BanUserAction::class, [
                    "objectIDs" => "%s"
                ]),
                "wcf.acp.user.ban",
                static fn(UserProfile $user) => !$user->banned && $user->canBan()
            ),
            new RpcInteraction(
                "unban",
                "core/users/%s/unban",
                "wcf.acp.user.unban",
                isAvailableCallback: static fn(UserProfile $user) => $user->banned && $user->canBan()
            ),
            new RpcInteraction(
                "confirm-email",
                "core/users/%s/confirm-email",
                "wcf.acp.user.action.confirmEmail",
                isAvailableCallback: static fn(UserProfile $user) => $user->canEnable() && !$user->isEmailConfirmed()
            ),
            new RpcInteraction(
                "unconfirm-email",
                "core/users/%s/unconfirm-email",
                "wcf.acp.user.action.unconfirmEmail",
                isAvailableCallback: static fn(UserProfile $user) => $user->canEnable() && $user->isEmailConfirmed()
            ),
            new RpcInteraction(
                "send-new-password",
                "core/users/%s/send-new-password",
                "wcf.acp.user.action.sendNewPassword",
                InteractionConfirmationType::Custom,
                "wcf.acp.user.action.sendNewPassword.confirmMessage",
                static fn(UserProfile $user) => WCF::getSession()->getPermission("admin.user.canEditPassword") &&
                    $user->userID !== WCF::getUser()->userID
            ),
            new LinkInteraction(
                "mail",
                UserMailForm::class,
                "wcf.acp.user.action.sendMail",
                static fn(UserProfile $user) => $user->userID !== WCF::getUser()->userID
                    && WCF::getSession()->getPermission("admin.user.canMailUser")
            ),
            new Divider(),
            new DeleteInteraction("core/users/%s", static fn(UserProfile $user) => $user->canDelete()),
            new FormBuilderDialogInteraction(
                "delete-content",
                LinkHandler::getInstance()->getControllerLink(DeleteUserContentAction::class, [
                    "objectIDs" => "%s"
                ]),
                "wcf.acp.content.removeContent",
                static fn(UserProfile $user) => $user->canDelete()
            ),
        ]);

        EventHandler::getInstance()->fire(
            new UserInteractionCollecting($this)
        );
    }

    #[\Override]
    public function getObjectClassName(): string
    {
        return UserProfile::class;
    }
}
