<?php

namespace wcf\system\email\transport;

use wcf\system\email\Email;
use wcf\system\email\Mailbox;

/**
 * An EmailTransport sends emails.
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
interface IEmailTransport
{
    /**
     * Delivers the given $email to the given Mailbox as the recipient.
     *
     * @param Email $email
     * @param Mailbox $envelopeFrom
     * @param Mailbox $envelopeTo
     * @return mixed
     */
    public function deliver(Email $email, Mailbox $envelopeFrom, Mailbox $envelopeTo);
}
