<?php

namespace wcf\data\contact\attachment;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of attachments of messages sent through the contact form.
 *
 * @author      Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       5.2
 *
 * @extends DatabaseObjectList<ContactAttachment>
 */
class ContactAttachmentList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ContactAttachment::class;
}
