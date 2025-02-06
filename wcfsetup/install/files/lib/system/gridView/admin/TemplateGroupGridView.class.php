<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\TemplateGroupEditForm;
use wcf\acp\page\TemplateListPage;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\template\group\I18nTemplateGroupList;
use wcf\data\template\group\TemplateGroup;
use wcf\event\gridView\admin\TemplateGroupGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\interaction\AbstractInteraction;
use wcf\system\interaction\admin\TemplateGroupInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Grid view for the list of template groups.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class TemplateGroupGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('templateGroupID')
                ->label('wcf.global.objectID')
                ->renderer(new ObjectIdColumnRenderer()),
            GridViewColumn::for('templateGroupName')
                ->label('wcf.global.name')
                ->titleColumn()
                ->filter(new I18nTextFilter())
                ->renderer(new PhraseColumnRenderer())
                ->sortable(sortByDatabaseColumn: 'templateGroupNameI18n'),
            GridViewColumn::for('templateGroupFolderName')
                ->label('wcf.acp.template.group.folderName')
                ->filter(new TextFilter())
                ->sortable(),
            GridViewColumn::for('templates')
                ->label('wcf.acp.template.group.templates')
                ->filter(new NumericFilter($this->subQueryTemplates()))
                ->sortable(sortByDatabaseColumn: $this->subQueryTemplates()),
            GridViewColumn::for('styles')
                ->label('wcf.acp.template.group.styles')
                ->filter(new NumericFilter($this->subQueryStyles()))
                ->sortable(sortByDatabaseColumn: $this->subQueryStyles()),
        ]);

        $provider = new TemplateGroupInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(
                TemplateGroupEditForm::class,
                static fn(TemplateGroup $group) => !$group->isImmutable()
            ),
        ]);
        $this->setInteractionProvider($provider);
        $this->addQuickInteraction(
            new class("showTemplates") extends AbstractInteraction {
                #[\Override]
                public function render(DatabaseObject $object): string
                {
                    \assert($object instanceof TemplateGroup);

                    return \sprintf(
                        '<a href="%s" title="%s" class="jsTooltip"><fa-icon name="list"></a>',
                        LinkHandler::getInstance()->getControllerLink(TemplateListPage::class, [
                            'filters' => ['templateGroupID' => $object->templateGroupID],
                        ]),
                        WCF::getLanguage()->get('wcf.acp.template.list'),
                    );
                }
            }
        );

        $this->setSortField("templateGroupName");
    }

    private function subQueryTemplates(): string
    {
        return "(
            SELECT  COUNT(*)
            FROM    wcf1_template
            WHERE   templateGroupID = template_group.templateGroupID
        )";
    }

    private function subQueryStyles(): string
    {
        return "(
            SELECT  COUNT(*)
            FROM    wcf1_style
            WHERE   templateGroupID = template_group.templateGroupID
        )";
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission("admin.template.canManageTemplate");
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        $list = new I18nTemplateGroupList();
        if (!empty($list->sqlSelects)) {
            $list->sqlSelects .= ', ';
        }

        $list->sqlSelects .= $this->subQueryStyles() . ' AS styles, ' . $this->subQueryTemplates() . ' AS templates';

        return $list;
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new TemplateGroupGridViewInitialized($this);
    }
}
