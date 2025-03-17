<?php

namespace wcf\system\email\transport;

use Laminas\Diactoros\Stream;
use Psr\Http\Message\StreamInterface;
use wcf\system\email\Email;
use wcf\system\email\Mailbox;
use wcf\util\DateUtil;

/**
 * DebugEmailTransport is a debug implementation of an email transport which writes emails into
 * a log file.
 *
 * @author  Tim Duesterhus, Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
final class DebugEmailTransport implements IEmailTransport
{
    private readonly StreamInterface $mbox;

    /**
     * Creates a new DebugTransport using the given mbox as target.
     *
     * @param $mbox mbox location or null for default location
     */
    public function __construct(?string $mbox = null)
    {
        if ($mbox === null) {
            $mbox = WCF_DIR . 'log/debug.mbox';
        }

        $this->mbox = new Stream($mbox, 'ab');
    }

    /**
     * Writes the given $email into the mbox.
     */
    public function deliver(Email $email, Mailbox $envelopeFrom, Mailbox $envelopeTo): void
    {
        $this->mbox->write(\sprintf(
            "From %s %s\r\n",
            $envelopeFrom->getAddress(),
            DateUtil::getDateTimeByTimestamp(TIME_NOW)->format('D M d H:i:s Y')
        ));
        $this->mbox->write("Delivered-To: " . $envelopeTo->getAddress() . "\r\n");
        $this->mbox->write($email->getEmail());
        $this->mbox->write("\r\n");
    }
}
