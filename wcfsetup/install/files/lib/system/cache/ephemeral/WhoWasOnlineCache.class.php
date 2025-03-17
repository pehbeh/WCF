<?php

namespace wcf\system\cache\ephemeral;

use Symfony\Contracts\Cache\ItemInterface;
use wcf\system\WCF;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractEphemeralCache<list<int>>
 */
final class WhoWasOnlineCache extends AbstractEphemeralCache
{
    #[\Override]
    public function __invoke(ItemInterface $item): array
    {
        $item->expiresAfter(600);
        $item->tag("user");

        $sql = "(
                    SELECT  userID
                    FROM    wcf1_user
                    WHERE   lastActivityTime > ?
                ) UNION (
                    SELECT  userID
                    FROM    wcf1_session
                    WHERE   userID IS NOT NULL
                        AND lastActivityTime > ?
                )";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute([TIME_NOW - 86400, TIME_NOW - USER_ONLINE_TIMEOUT]);

        return $statement->fetchAll(\PDO::FETCH_COLUMN);
    }
}
