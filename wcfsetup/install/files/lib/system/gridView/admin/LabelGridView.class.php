<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\LabelEditForm;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\label\group\ViewableLabelGroup;
use wcf\data\label\I18nLabelList;
use wcf\data\label\Label;
use wcf\system\cache\builder\LabelCacheBuilder;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\GridViewSortButton;
use wcf\system\gridView\renderer\AbstractColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of labels.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class LabelGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("labelID")
                ->label("wcf.global.objectID")
                ->renderer(new ObjectIdColumnRenderer())
                ->filter(new ObjectIdFilter())
                ->sortable(),
            GridViewColumn::for("label")
                ->label("wcf.acp.label.label")
                ->titleColumn()
                ->filter(new I18nTextFilter())
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Label);
                            $badgeClasName = StringUtil::encodeHTML($row->getClassNames());
                            $label = StringUtil::encodeHTML($row->getTitle());

                            return <<<HTML
                                <span class="badge label {$badgeClasName}">{$label}</span>
                            HTML;
                        }
                    }
                )
                ->sortable(sortByDatabaseColumn: "labelI18n"),
            GridViewColumn::for("groupID")
                ->label("wcf.acp.label.group")
                ->renderer(
                    new class extends AbstractColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Label);

                            $groups = LabelCacheBuilder::getInstance()->getData(arrayIndex: "groups");
                            $group = $groups[$row->groupID];
                            \assert($group instanceof ViewableLabelGroup);

                            if (empty($group->groupDescription)) {
                                return StringUtil::encodeHTML($group->getTitle());
                            }
                            return \sprintf(
                                "%s / %s",
                                StringUtil::encodeHTML($group->getTitle()),
                                StringUtil::encodeHTML($group->groupDescription)
                            );
                        }
                    }
                )
                ->sortable(),
            GridViewColumn::for("showOrder")
                ->label("wcf.global.showOrder")
                ->renderer(new NumberColumnRenderer())
                ->filter(new NumericFilter())
                ->sortable(),
        ]);

        // TODO add interaction provider

        $this->setSortButton(new GridViewSortButton("showOrder", "core/labels/sort", filterColumns: ["groupID"]));

        $this->setSortField("label");
        $this->addRowLink(new GridViewRowLink(LabelEditForm::class));
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission("admin.content.label.canManageLabel");
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        return new I18nLabelList();
    }
}
