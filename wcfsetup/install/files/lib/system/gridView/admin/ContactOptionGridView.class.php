<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\ContactOptionEditForm;
use wcf\data\contact\option\ContactOption;
use wcf\data\contact\option\ContactOptionList;
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
                ->sortable(),
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

        $this->addRowLink(new GridViewRowLink(ContactOptionEditForm::class));

        $this->setSortField("showOrder");
        $this->setSortOrder("ASC");
    }

    #[\Override]
    protected function createObjectList(): ContactOptionList
    {
        // TODO 18n list
        return new ContactOptionList();
    }
}
