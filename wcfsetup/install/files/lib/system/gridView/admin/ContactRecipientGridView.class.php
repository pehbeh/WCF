<?php

namespace wcf\system\gridView\admin;

use wcf\acp\form\ContactRecipientEditForm;
use wcf\data\contact\recipient\ContactRecipient;
use wcf\data\contact\recipient\ContactRecipientList;
use wcf\system\gridView\AbstractGridView;
use wcf\system\gridView\filter\I18nTextFilter;
use wcf\system\gridView\filter\NumericFilter;
use wcf\system\gridView\filter\ObjectIdFilter;
use wcf\system\gridView\GridViewColumn;
use wcf\system\gridView\GridViewRowLink;
use wcf\system\gridView\renderer\EmailColumnRenderer;
use wcf\system\gridView\renderer\NumberColumnRenderer;
use wcf\system\gridView\renderer\ObjectIdColumnRenderer;
use wcf\system\gridView\renderer\PhraseColumnRenderer;

/**
 * Grid view for the list of contact recipients.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractGridView<ContactRecipient, ContactRecipientList>
 */
final class ContactRecipientGridView extends AbstractGridView
{
    public function __construct()
    {
        $this->addColumns([
            GridViewColumn::for("recipientID")
                ->label("wcf.global.objectID")
                ->renderer(new ObjectIdColumnRenderer())
                ->filter(new ObjectIdFilter())
                ->sortable(),
            GridViewColumn::for("name")
                ->label("wcf.global.name")
                ->filter(new I18nTextFilter())
                ->titleColumn()
                ->renderer(new PhraseColumnRenderer())
                ->sortable(),
            GridViewColumn::for("email")
                ->label("wcf.user.email")
                ->renderer(new EmailColumnRenderer())
                ->sortable(),
            GridViewColumn::for("showOrder")
                ->label("wcf.acp.customOption.showOrder")
                ->filter(new NumericFilter())
                ->renderer(new NumberColumnRenderer())
                ->sortable(),
        ]);

        $this->addRowLink(new GridViewRowLink(ContactRecipientEditForm::class));

        $this->setSortField("showOrder");
        $this->setSortOrder("ASC");
    }

    #[\Override]
    protected function createObjectList(): ContactRecipientList
    {
        // TODO 18n list
        return new ContactRecipientList();
    }
}
