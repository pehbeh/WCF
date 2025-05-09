<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\UserRankEditForm;
use wcf\data\DatabaseObject;
use wcf\data\user\group\UserGroup;
use wcf\data\user\rank\UserRank;
use wcf\data\user\rank\UserRankList;
use wcf\event\gridView\admin\UserRankGridViewInitialized;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\admin\UserRankInteractions;
use wcf\system\interaction\bulk\admin\UserRankBulkInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\language\MultilingualHelper;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of user ranks.
 *
 * @author      Marcel Werk
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 *
 * @extends AbstractGridView<UserRank, UserRankList>
 */
final class UserRankGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('rankID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('rankTitle')
                ->label('wcf.acp.user.rank.title')
                ->sortable(sortByDatabaseColumn: $this->subqueryRankTitle())
                ->titleColumn()
                ->filter(new TextFilter($this->subqueryRankTitle()))
                ->renderer([
                    new class extends DefaultColumnRenderer {
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserRank);

                            return '<span class="badge label' . ($row->cssClassName ? ' ' . $row->cssClassName : '') . '">'
                                . StringUtil::encodeHTML($row->getTitle())
                                . '<span>';
                        }
                    }
                ]),
            GridViewColumn::for('rankImage')
                ->label('wcf.acp.user.rank.image')
                ->sortable()
                ->renderer([
                    new class extends DefaultColumnRenderer {
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserRank);

                            return $row->rankImage ? $row->getImage() : '';
                        }
                    },
                ]),
            GridViewColumn::for('groupID')
                ->label('wcf.user.group')
                ->sortable()
                ->filter(new SelectFilter($this->getAvailableUserGroups()))
                ->renderer([
                    new class extends DefaultColumnRenderer {
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            return StringUtil::encodeHTML(UserGroup::getGroupByID($value)->getName());
                        }
                    },
                ]),
            GridViewColumn::for('requiredGender')
                ->label('wcf.user.option.gender')
                ->sortable()
                ->renderer([
                    new class extends DefaultColumnRenderer {
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            if (!$value) {
                                return '';
                            }

                            return WCF::getLanguage()->get(match ($value) {
                                1 => 'wcf.user.gender.male',
                                2 => 'wcf.user.gender.female',
                                default => 'wcf.user.gender.other'
                            });
                        }
                    },
                ]),
            GridViewColumn::for('requiredPoints')
                ->label('wcf.acp.user.rank.requiredPoints')
                ->sortable()
                ->renderer(new NumberColumnRenderer()),
        ]);

        $provider = new UserRankInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(UserRankEditForm::class)
        ]);
        $this->setInteractionProvider($provider);
        $this->setBulkInteractionProvider(new UserRankBulkInteractions());
        $this->addRowLink(new GridViewRowLink(UserRankEditForm::class));
        $this->setSortField('rankTitle');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return \MODULE_USER_RANK && WCF::getSession()->getPermission('admin.user.rank.canManageRank');
    }

    #[\Override]
    protected function createObjectList(): UserRankList
    {
        return new UserRankList();
    }

    #[\Override]
    protected function getInitializedEvent(): UserRankGridViewInitialized
    {
        return new UserRankGridViewInitialized($this);
    }

    /**
     * @return array<int, string>
     */
    private function getAvailableUserGroups(): array
    {
        $groups = [];
        foreach (UserGroup::getSortedGroupsByType([], [UserGroup::GUESTS, UserGroup::EVERYONE]) as $group) {
            $groups[$group->groupID] = $group->getName();
        }

        return $groups;
    }

    private function subqueryRankTitle(): string
    {
        return MultilingualHelper::subqueryForContentTable(
            "title",
            "wcf1_user_rank_content",
            "rankID",
            "user_rank",
        );
    }
}
