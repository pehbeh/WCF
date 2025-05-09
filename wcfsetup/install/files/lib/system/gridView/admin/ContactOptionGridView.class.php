<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\ContactOptionEditForm;
use wcf\data\contact\option\ContactOption;
use wcf\data\contact\option\ContactOptionList;
use wcf\event\gridView\admin\ContactOptionGridViewInitialized;
use wcf\system\form\option\FormOptionHandler;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\filter\SelectFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;
use wcf\system\interaction\admin\ContactOptionInteractions;
use wcf\system\interaction\Divider;
use wcf\system\interaction\EditInteraction;
use wcf\system\WCF;

/**
 * Grid view for the list of contact options.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractGridView<ContactOption, ContactOptionList>
 */
final class ContactOptionGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("optionID")
                ->label("wcf.global.objectID")
                ->renderer(new ObjectIdColumnRenderer())
                ->filter(new ObjectIdFilter())
                ->sortable(),
            GridViewColumn::for("optionTitle")
                ->label("wcf.global.name")
                ->renderer(new PhraseColumnRenderer())
                ->filter(new I18nTextFilter())
                ->titleColumn()
                ->sortable(sortByDatabaseColumn: $this->subqueryOptionTitle()),
            GridViewColumn::for("optionType")
                ->label("wcf.acp.customOption.optionType")
                ->filter(new SelectFilter(FormOptionHandler::getInstance()->getSortedOptionTypes()))
                ->sortable(),
            GridViewColumn::for("showOrder")
                ->label("wcf.acp.customOption.showOrder")
                ->filter(new NumericFilter())
                ->renderer(new NumberColumnRenderer())
                ->sortable(),
        ]);

        $provider = new ContactOptionInteractions();
        $provider->addInteractions([
            new Divider(),
            new EditInteraction(ContactOptionEditForm::class),
        ]);
        $this->setInteractionProvider($provider);

        $this->addRowLink(new GridViewRowLink(ContactOptionEditForm::class));

        $this->setSortField("showOrder");
        $this->setSortOrder("ASC");
    }

    #[\Override]
    public function isAccessible(): bool
    {
        return \MODULE_CONTACT_FORM
            && WCF::getSession()->getPermission("admin.contact.canManageContactForm");
    }

    #[\Override]
    protected function createObjectList(): ContactOptionList
    {
        return new ContactOptionList();
    }

    private function subqueryOptionTitle(): string
    {
        $languageID = WCF::getLanguage()->languageID;

        return "
            COALESCE((
                SELECT languageItemValue
                FROM   wcf1_language_item
                WHERE  languageItem = contact_option.optionTitle
                AND    languageID = {$languageID}
            ), contact_option.optionTitle)
        ";
    }

    #[\Override]
    protected function getInitializedEvent(): ContactOptionGridViewInitialized
    {
        return new ContactOptionGridViewInitialized($this);
    }
}
