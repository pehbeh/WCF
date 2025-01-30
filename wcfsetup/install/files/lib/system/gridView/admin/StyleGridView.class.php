<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\StyleEditForm;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\style\Style;
use wcf\data\style\StyleList;
use wcf\event\gridView\admin\StyleGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ILinkColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\admin\StyleInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of styles.
 *
 * @author      Marcel Werk
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class StyleGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('styleID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('styleName')
                ->label('wcf.global.name')
                ->sortable()
                ->titleColumn()
                ->renderer([
                    new class extends DefaultColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Style);

                            if ($row->isDefault) {
                                $value .= \sprintf(
                                    ' <span class="badge">%s</span>',
                                    WCF::getLanguage()->get('wcf.global.defaultValue')
                                );
                            }

                            if (!$row->styleDescription) {
                                return $value;
                            }

                            return \sprintf(
                                '%s<br><small>%s</small>',
                                $value,
                                StringUtil::encodeHTML(WCF::getLanguage()->get($row->styleDescription))
                            );
                        }
                    },
                ])
                ->filter(new TextFilter()),
            GridViewColumn::for('styleVersion')
                ->label('wcf.acp.style.styleVersion')
                ->sortable(),
            GridViewColumn::for('styleDate')
                ->label('wcf.global.date')
                ->sortable(),
            GridViewColumn::for('authorName')
                ->label('wcf.acp.style.authorName')
                ->sortable()
                ->renderer([
                    new class extends DefaultColumnRenderer implements ILinkColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Style);

                            if (!$row->authorURL) {
                                return $value;
                            }

                            return \sprintf(
                                '<a href="%s" class="externalURL">%s</a>',
                                StringUtil::encodeHTML($row->authorURL),
                                $value
                            );
                        }
                    },
                ])
                ->filter(new TextFilter()),
            GridViewColumn::for('users')
                ->label('wcf.acp.style.users')
                ->sortable(true, 'users')
                ->renderer(new NumberColumnRenderer()),
        ]);

        $provider = new StyleInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(StyleEditForm::class)
        ]);
        $this->setInteractionProvider($provider);
        $this->addQuickInteraction(
            new ToggleInteraction(
                'enable',
                'core/styles/%s/enable',
                'core/styles/%s/disable',
                isAvailableCallback: static fn(Style $object) => !$object->isDefault
            )
        );
        $this->addRowLink(new GridViewRowLink(StyleEditForm::class));
        $this->setSortField('styleName');
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.style.canManageStyle');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        $list = new StyleList();
        $list->sqlSelects = "(
            SELECT  COUNT(*)
            FROM    wcf1_user
            WHERE   styleID = style.styleID
        ) AS users";

        return $list;
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new StyleGridViewInitialized($this);
    }
}
