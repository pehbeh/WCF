<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\UserGroupEditForm;
use wcf\acp\form\UserSearchForm;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\user\group\I18nUserGroupList;
use wcf\data\user\group\UserGroup;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\ILinkColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\interaction\admin\UserGroupInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Grid view for the list of user groups.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class UserGroupGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("groupID")
                ->label("wcf.global.objectID")
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for("isOwner")
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserGroup);
                            if (!$row->isOwner()) {
                                return "";
                            }

                            $title = WCF::getLanguage()->get("wcf.acp.group.type.owner");
                            return <<<HTML
                                <span  class="jsTooltip" title="{$title}">
                                    <fa-icon name="shield-halved"></fa-icon>
                                </span>
                            HTML;
                        }

                        #[\Override]
                        public function getClasses(): string
                        {
                            return 'gridView__column--digits';
                        }
                    }
                ),
            GridViewColumn::for("groupName")
                ->label("wcf.global.name")
                ->titleColumn()
                ->filter(new I18nTextFilter())
                ->renderer(new PhraseColumnRenderer())
                ->sortable(sortByDatabaseColumn: "groupNameI18n"),
            GridViewColumn::for("members")
                ->label("wcf.acp.group.members")
                ->renderer(
                    new class extends NumberColumnRenderer implements ILinkColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof UserGroup);

                            if ($row->groupType === 1 || $row->groupType === 2) {
                                return parent::render($value, $row);
                            }

                            return \sprintf(
                                '<a class="jsTooltip" title="%s" href="%s">%s</a>',
                                WCF::getLanguage()->get("wcf.acp.group.showMembers"),
                                LinkHandler::getInstance()->getControllerLink(
                                    UserSearchForm::class,
                                    ["groupID" => $row->groupID]
                                ),
                                parent::render($value, $row)
                            );
                        }
                    }
                )
                ->filter(new NumericFilter($this->subSelectMembers()))
                ->sortable(sortByDatabaseColumn: $this->subSelectMembers()),
            GridViewColumn::for("priority")
                ->label("wcf.acp.group.priority")
                ->filter(new NumericFilter())
                ->renderer(new NumberColumnRenderer())
                ->sortable(),
        ]);

        $provider = new UserGroupInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(UserGroupEditForm::class, static fn(UserGroup $group) => $group->isEditable())
        ]);
        $this->setInteractionProvider($provider);

        $this->setSortField("groupName");
    }

    private function subSelectMembers(): string
    {
        return "(
            SELECT  COUNT(*)
            FROM    wcf1_user_to_group
            WHERE   groupID = user_group.groupID
        )";
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        $list = new I18nUserGroupList();

        if (!empty($list->sqlSelects)) {
            $list->sqlSelects .= ", ";
        }
        $list->sqlSelects .= $this->subSelectMembers() . " AS members";

        return $list;
    }
}
