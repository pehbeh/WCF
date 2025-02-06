<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\UserEditForm;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\user\UserProfile;
use wcf\data\user\UserProfileList;
use wcf\event\gridView\admin\UserGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\filter\TimeFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\EmailColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\TimeColumnRenderer;
use wcf\system\gridView\renderer\UserLinkColumnRenderer;
use wcf\system\interaction\admin\UserInteractions;
use wcf\system\interaction\bulk\admin\UserBulkInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\WCF;

/**
 * Grid view for the list of users
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("userID")
                ->label("wcf.global.objectID")
                ->filter(new ObjectIdFilter())
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for("avatar")
                ->label("wcf.user.avatar")
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserProfile);
                            $avatar = $row->getAvatar();

                            return $avatar->getImageTag(24);
                        }

                        #[\Override]
                        public function getClasses(): string
                        {
                            return 'gridView__column--digits';
                        }
                    }
                ),
            GridViewColumn::for("username")
                ->label("wcf.user.username")
                ->titleColumn()
                ->renderer(
                    new class(UserEditForm::class) extends UserLinkColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserProfile);

                            return parent::render($row->userID, $row);
                        }
                    }
                )
                ->filter(new TextFilter())
                ->sortable(),
        ]);

        if (WCF::getSession()->getPermission('admin.user.canEditMailAddress')) {
            $this->addColumn(
                GridViewColumn::for("email")
                    ->label("wcf.user.email")
                    ->filter(new TextFilter())
                    ->renderer(new EmailColumnRenderer())
                    ->sortable()
            );
        }

        $this->addColumns([
            GridViewColumn::for("activityPoints")
                ->label("wcf.user.activityPoint")
                ->filter(new NumericFilter())
                ->renderer(new NumberColumnRenderer())
                ->sortable(),
            GridViewColumn::for("likesReceived")
                ->label("wcf.like.likesReceived")
                ->filter(new NumericFilter())
                ->renderer(new NumberColumnRenderer())
                ->hidden(),
            GridViewColumn::for("profileHits")
                ->label("wcf.user.profileHits")
                ->filter(new NumericFilter())
                ->renderer(new NumberColumnRenderer())
                ->hidden(),
            GridViewColumn::for("registrationDate")
                ->label("wcf.user.registrationDate")
                ->filter(new TimeFilter())
                ->renderer(new TimeColumnRenderer())
                ->sortable(),
            GridViewColumn::for("lastActivityTime")
                ->label("wcf.user.lastActivityTime")
                ->filter(new TimeFilter())
                ->renderer(
                    new class extends TimeColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserProfile);
                            if ($row->isOnline()) {
                                $title = WCF::getLanguage()->get("wcf.user.online");

                                return <<<HTML
                                    {$title}
                                    <span class="userOnlineIndicator" aria-hidden="true"></span>
                                HTML;
                            }

                            return parent::render($value, $row);
                        }
                    }
                )
                ->sortable()
        ]);

        $provider = new UserInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(UserEditForm::class, static fn(UserProfile $user) => $user->canEdit())
        ]);
        $this->setInteractionProvider($provider);

        $this->addQuickInteraction(
            new ToggleInteraction(
                "enable",
                "core/users/%s/enable",
                "core/users/%s/disable",
                isAvailableCallback: static fn(UserProfile $user) => $user->canEnable()
            )
        );
        $this->setBulkInteractionProvider(new UserBulkInteractions());

        $this->setSortField("username");
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new UserProfileList();
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new UserGridViewInitialized($this);
    }
}
