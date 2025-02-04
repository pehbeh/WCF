<?php

namespace wcf\system\view\user\profile;

use wcf\acp\form\UserEditForm;
use wcf\action\UserFollowAction;
use wcf\action\UserIgnoreAction;
use wcf\data\user\group\UserGroup;
use wcf\data\user\UserProfile;
use wcf\event\user\profile\UserProfileHeaderInteractionOptionCollecting;
use wcf\event\user\profile\UserProfileHeaderManagementOptionCollecting;
use wcf\event\user\profile\UserProfileHeaderSearchContentLinkCollecting;
use wcf\event\user\profile\UserProfileStatItemCollecting;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents the view for the user profile header.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserProfileHeaderView
{
    /**
     * @var UserProfileStatItem[]
     */
    private array $statItems = [];

    /**
     * @var UserProfileHeaderViewSearchContentLink[]
     */
    private array $searchContentLinks = [];

    /**
     * @var UserProfileHeaderViewInteractionOption[]
     */
    private array $interactionOptions = [];

    /**
     * @var UserProfileHeaderViewManagementOption[]
     */
    private array $managementOptions = [];

    public function __construct(
        public readonly UserProfile $user,
    ) {
        $this->initStatItems();
        $this->initSearchContentLinks();
        $this->initInteractionOptions();
        $this->initManagementOptions();
    }

    public function __toString(): string
    {
        return WCF::getTPL()->render('wcf', 'userProfileHeader', [
            'view' => $this,
        ]);
    }

    public function hasStatItems(): bool
    {
        return $this->statItems !== [];
    }

    /**
     * @return UserProfileStatItem[]
     */
    public function getStatItems(): array
    {
        return $this->statItems;
    }

    public function hasSearchContentLinks(): bool
    {
        return $this->searchContentLinks !== [];
    }

    /**
     * @return UserProfileHeaderViewSearchContentLink[]
     */
    public function getSearchContentLinks(): array
    {
        return $this->searchContentLinks;
    }

    public function hasInteractionOptions(): bool
    {
        return $this->interactionOptions !== [];
    }

    /**
     * @return UserProfileHeaderViewInteractionOption[]
     */
    public function getInteractionOptions(): array
    {
        return $this->interactionOptions;
    }

    public function hasManagementOptions(): bool
    {
        return $this->managementOptions !== [];
    }

    /**
     * @return UserProfileHeaderViewManagementOption[]
     */
    public function getManagementOptions(): array
    {
        return $this->managementOptions;
    }

    public function canEditUser(): bool
    {
        return $this->user->canEdit() || (WCF::getUser()->userID == $this->user->userID && $this->user->canEditOwnProfile());
    }

    public function canEditCoverPhoto(): bool
    {
        return $this->user->canEdit() || (WCF::getUser()->userID == $this->user->userID && (WCF::getSession()->getPermission('user.profile.coverPhoto.canUploadCoverPhoto') || $this->user->coverPhotoHash));
    }

    public function canAddCoverPhoto(): bool
    {
        return $this->user->canEdit() || (WCF::getUser()->userID == $this->user->userID && WCF::getSession()->getPermission('user.profile.coverPhoto.canUploadCoverPhoto'));
    }

    public function isInAccessibleGroup(): bool
    {
        return UserGroup::isAccessibleGroup($this->user->getGroupIDs());
    }

    private function initStatItems(): void
    {
        $event = new UserProfileStatItemCollecting($this->user);
        EventHandler::getInstance()->fire($event);
        if ($event->getItems() !== []) {
            $this->statItems = \array_merge($this->statItems, $event->getItems());
        }
    }

    private function initSearchContentLinks(): void
    {
        $event = new UserProfileHeaderSearchContentLinkCollecting($this->user);
        EventHandler::getInstance()->fire($event);
        $this->searchContentLinks = $event->getLinks();
    }

    private function initInteractionOptions(): void
    {
        if ($this->user->userID != WCF::getUser()->userID) {
            if ($this->user->isAccessible('canViewEmailAddress') || WCF::getSession()->getPermission('admin.user.canEditMailAddress')) {
                $this->interactionOptions[] = UserProfileHeaderViewInteractionOption::forLink(
                    WCF::getLanguage()->get('wcf.user.button.mail'),
                    'mailto:' . $this->user->email
                );
            }

            if (WCF::getSession()->getPermission('user.profile.canReportContent')) {
                $this->interactionOptions[] = UserProfileHeaderViewInteractionOption::forButton(
                    WCF::getLanguage()->get('wcf.user.profile.report'),
                    'data-report-content="com.woltlab.wcf.user" data-object-id="' . $this->user->userID . '"'
                );
            }

            if (WCF::getUser()->userID && !$this->user->isIgnoredUser(WCF::getUser()->userID)) {
                if ($this->user->isFollower(WCF::getUser()->userID)) {
                    $label = 'wcf.user.button.unfollow';
                    $value = 1;
                } else {
                    $label = 'wcf.user.button.follow';
                    $value = 0;
                }

                $this->interactionOptions[] = UserProfileHeaderViewInteractionOption::forButton(
                    WCF::getLanguage()->get($label),
                    \sprintf(
                        'data-following="%d" data-follow-user="%s" data-type="button"',
                        $value,
                        StringUtil::encodeHTML(
                            LinkHandler::getInstance()->getControllerLink(UserFollowAction::class, ['id' => $this->user->userID])
                        )
                    )
                );
            }

            if (WCF::getUser()->userID && !$this->user->getPermission('user.profile.cannotBeIgnored')) {
                if ($this->user->isIgnoredByUser(WCF::getUser()->userID)) {
                    $label = 'wcf.user.button.unignore';
                    $value = 1;
                } else {
                    $label = 'wcf.user.button.ignore';
                    $value = 0;
                }

                $this->interactionOptions[] = UserProfileHeaderViewInteractionOption::forButton(
                    WCF::getLanguage()->get($label),
                    \sprintf(
                        'data-ignored="%d" data-ignore-user="%s" data-type="button"',
                        $value,
                        StringUtil::encodeHTML(
                            LinkHandler::getInstance()->getControllerLink(UserIgnoreAction::class, ['id' => $this->user->userID])
                        )
                    )
                );
            }
        }

        $event = new UserProfileHeaderInteractionOptionCollecting($this->user);
        EventHandler::getInstance()->fire($event);
        if ($event->getOptions() !== []) {
            $this->interactionOptions = \array_merge($this->interactionOptions, $event->getOptions());
        }
    }

    private function initManagementOptions(): void
    {
        if (!$this->isInAccessibleGroup() || $this->user->userID == WCF::getUser()->userID) {
            return;
        }

        if (WCF::getSession()->getPermission('admin.user.canBanUser')) {
            $this->managementOptions[] = UserProfileHeaderViewManagementOption::forButton(
                WCF::getLanguage()->get($this->user->banned ? 'wcf.user.unban' : 'wcf.user.ban'),
                'class="jsButtonUserBan"',
            );
        }
        if (WCF::getSession()->getPermission('admin.user.canDisableAvatar')) {
            $this->managementOptions[] = UserProfileHeaderViewManagementOption::forButton(
                WCF::getLanguage()->get($this->user->disableAvatar ? 'wcf.user.enableAvatar' : 'wcf.user.disableAvatar'),
                'class="jsButtonUserDisableAvatar"',
            );
        }
        if (WCF::getSession()->getPermission('admin.user.canDisableSignature')) {
            $this->managementOptions[] = UserProfileHeaderViewManagementOption::forButton(
                WCF::getLanguage()->get($this->user->disableSignature ? 'wcf.user.enableSignature' : 'wcf.user.disableSignature'),
                'class="jsButtonUserDisableSignature"',
            );
        }
        if (WCF::getSession()->getPermission('admin.user.canDisableCoverPhoto')) {
            $this->managementOptions[] = UserProfileHeaderViewManagementOption::forButton(
                WCF::getLanguage()->get($this->user->disableCoverPhoto ? 'wcf.user.enableCoverPhoto' : 'wcf.user.disableCoverPhoto'),
                'class="jsButtonUserDisableCoverPhoto"',
            );
        }
        if (WCF::getSession()->getPermission('admin.user.canEnableUser')) {
            $this->managementOptions[] = UserProfileHeaderViewManagementOption::forButton(
                WCF::getLanguage()->get($this->user->pendingActivation() ? 'wcf.acp.user.enable' : 'wcf.acp.user.disable'),
                'class="jsButtonUserEnable"',
            );
        }
        if (WCF::getSession()->getPermission('admin.general.canUseAcp') && WCF::getSession()->getPermission('admin.user.canEditUser')) {
            $this->managementOptions[] = UserProfileHeaderViewManagementOption::forLink(
                WCF::getLanguage()->get('wcf.user.edit'),
                LinkHandler::getInstance()->getControllerLink(UserEditForm::class, ['id' => $this->user->userID]),
            );
        }

        $event = new UserProfileHeaderManagementOptionCollecting($this->user);
        EventHandler::getInstance()->fire($event);
        if ($event->getOptions() !== []) {
            $this->managementOptions = \array_merge($this->managementOptions, $event->getOptions());
        }
    }
}
