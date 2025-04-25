<?php

namespace wcf\data\contact\attachment;

use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit attachments attached to messages sent through the contact form.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.2
 * @deprecated  6.2 Contact form attachments are using `ContactFormFileProcessor` instead.
 *
 * @mixin ContactAttachment
 * @extends DatabaseObjectEditor<ContactAttachment>
 */
class ContactAttachmentEditor extends DatabaseObjectEditor
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = ContactAttachment::class;
}
