<?php

namespace wcf\system\email\transport;

use wcf\system\email\Email;
use wcf\system\email\EmailGrammar;
use wcf\system\email\Mailbox;
use wcf\system\email\transport\exception\TransientFailure;
use wcf\util\StringUtil;

/**
 * PhpEmailTransport is an implementation of an email transport which sends emails using mail().
 *
 * @author  Tim Duesterhus
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since   3.0
 */
final class PhpEmailTransport implements IEmailTransport
{
    /**
     * Delivers the given email via mail().
     *
     * @throws  TransientFailure
     */
    public function deliver(Email $email, Mailbox $envelopeFrom, Mailbox $envelopeTo): void
    {
        $headers = \array_filter($email->getHeaders(), static function ($item) {
            // filter out headers that are either
            //   a) automatically added by PHP
            //   b) interpreted by sendmail because of -t
            //
            // The email will be slightly mangled as the result of this. In particular
            // the 'To' and 'Cc' headers will be cleared, which makes this email appear
            // to be sent to a single recipient only.
            // But this is better than crippling the superior transports or special casing
            // the PhpTransport in other classes.
            return $item[0] !== 'subject' && $item[0] !== 'to' && $item[0] !== 'cc' && $item[0] !== 'bcc';
        });

        $headers = \implode("\r\n", \array_map(static function ($item) {
            [$name, $value] = $item;

            $name = Email::getCanonicalHeaderName($name);

            return $name . ': ' . $value;
        }, $headers));

        $encodedSubject = EmailGrammar::encodeQuotedPrintableHeader($email->getSubject());

        if (MAIL_USE_F_PARAM) {
            $return = \mail(
                $envelopeTo->getAddress(),
                $encodedSubject,
                StringUtil::unifyNewlines($email->getBodyString()),
                $headers,
                '-f' . $envelopeFrom->getAddress()
            );
        } else {
            $return = \mail(
                $envelopeTo->getAddress(),
                $encodedSubject,
                StringUtil::unifyNewlines($email->getBodyString()),
                $headers
            );
        }

        if (!$return) {
            throw new TransientFailure("mail() returned false");
        }
    }
}
