<?php

namespace wcf\data\contact\attachment;

use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes actions on attachments of messages sent through the contact form.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.2
 *
 * @extends AbstractDatabaseObjectAction<ContactAttachment, ContactAttachmentEditor>
 */
class ContactAttachmentAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = ContactAttachmentEditor::class;
}
