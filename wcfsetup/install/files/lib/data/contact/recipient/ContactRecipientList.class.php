<?php

namespace wcf\data\contact\recipient;

use wcf\data\DatabaseObjectList;

/**
 * Represents a list of contact recipients.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.1
 *
 * @extends DatabaseObjectList<ContactRecipient>
 */
class ContactRecipientList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = ContactRecipient::class;

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'showOrder';
}
