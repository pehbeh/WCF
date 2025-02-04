<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\LanguageEditForm;
use wcf\acp\page\LanguageItemListPage;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectList;
use wcf\data\language\Language;
use wcf\data\language\LanguageList;
use wcf\event\gridView\admin\LanguageGridViewInitialized;
use wcf\event\IPsr14Event;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\filter\TextFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\DefaultColumnRenderer;
use wcf\system\gridView\renderer\ILinkColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\interaction\admin\LanguageInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\interaction\ToggleInteraction;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Grid view for the list of languages.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
final class LanguageGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for('languageID')
                ->label('wcf.global.objectID')
                ->filter(new ObjectIdFilter())
                ->renderer(new ObjectIdColumnRenderer())
                ->sortable(),
            GridViewColumn::for('languageName')
                ->label('wcf.global.name')
                ->filter(new TextFilter())
                ->titleColumn()
                ->sortable(),
            GridViewColumn::for('languageCode')
                ->label('wcf.acp.language.code')
                ->filter(new TextFilter())
                ->sortable(),
            GridViewColumn::for('users')
                ->label('wcf.acp.language.users')
                ->filter(new NumericFilter($this->subSelectUsers()))
                ->renderer(new NumberColumnRenderer())
                ->sortable(sortByDatabaseColumn: $this->subSelectUsers()),
            GridViewColumn::for('variables')
                ->label('wcf.acp.language.variables')
                ->filter(new NumericFilter($this->subSelectVariables()))
                ->renderer(
                    new class extends NumberColumnRenderer implements ILinkColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Language);

                            $href = LinkHandler::getInstance()->getControllerLink(
                                LanguageItemListPage::class,
                                ["filters" => ["languageID" => $row->languageID]]
                            );

                            return '<a href="' . StringUtil::encodeHTML($href) . '">'
                                . StringUtil::formatNumeric($value)
                                . '</a>';
                        }
                    }
                )
                ->sortable(sortByDatabaseColumn: $this->subSelectVariables()),
            GridViewColumn::for('customVariables')
                ->label('wcf.acp.language.customVariables')
                ->filter(new NumericFilter($this->subSelectCustomVariables()))
                ->renderer(
                    new class extends NumberColumnRenderer implements ILinkColumnRenderer {
                        #[\Override]
                        public function render(mixed $value, DatabaseObject $row): string
                        {
                            \assert($row instanceof Language);

                            $href = LinkHandler::getInstance()->getControllerLink(
                                LanguageItemListPage::class,
                                ["filters" => ["languageID" => $row->languageID, "languageUseCustomValue" => 1]]
                            );

                            return '<a href="' . StringUtil::encodeHTML($href) . '">'
                                . StringUtil::formatNumeric($value)
                                . '</a>';
                        }
                    }
                )
                ->sortable(sortByDatabaseColumn: $this->subSelectCustomVariables())
        ]);

        $provider = new LanguageInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(LanguageEditForm::class)
        ]);
        $this->setInteractionProvider($provider);
        $this->addQuickInteraction(
            new ToggleInteraction(
                "enable",
                "core/languages/%s/enable",
                "core/languages/%s/disable",
                isAvailableCallback: static function (Language $language) {
                    return !$language->isDefault;
                }
            )
        );

        $this->addRowLink(new GridViewRowLink(LanguageEditForm::class));
        $this->setSortField('languageName');
    }

    private function subSelectUsers(): string
    {
        return "(
            SELECT  COUNT(*)
            FROM    wcf1_user user
            WHERE   languageID = language.languageID
        )";
    }

    private function subSelectVariables(): string
    {
        return "(
            SELECT  COUNT(*)
            FROM    wcf1_language_item
            WHERE   languageID = language.languageID
        )";
    }

    private function subSelectCustomVariables(): string
    {
        return "(
            SELECT  COUNT(*)
            FROM    wcf1_language_item
            WHERE   languageID = language.languageID
                AND languageCustomItemValue IS NOT NULL
        )";
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return WCF::getSession()->getPermission('admin.language.canManageLanguage');
    }

    #[\Override]
    protected function createObjectList(): DatabaseObjectList
    {
        $list = new LanguageList();
        $list->sqlSelects = \sprintf(
            "%s as users, %s as variables, %s as customVariables",
            $this->subSelectUsers(),
            $this->subSelectVariables(),
            $this->subSelectCustomVariables()
        );

        return $list;
    }

    #[\Override]
    protected function getInitializedEvent(): ?IPsr14Event
    {
        return new LanguageGridViewInitialized($this);
    }
}
