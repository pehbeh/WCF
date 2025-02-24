<?php

namespace wcf\system\cache\tolerant;

use Symfony\Contracts\Cache\ItemInterface;
use wcf\data\user\User;
use wcf\system\WCF;

/**
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 *
 * @extends AbstractTolerantCache<array<int, list<int>>>
 */
final class UserBirthdayCache extends AbstractTolerantCache
{
    public function __construct(public readonly int $month)
    {
    }

    #[\Override]
    public function __invoke(ItemInterface $item): array
    {
        $item->expiresAfter(3600);
        $item->tag("user");

        $userOptionID = User::getUserOptionID('birthday');
        if ($userOptionID === null) {
            // birthday profile field missing; skip
            return [];
        }

        $data = [];
        $birthday = 'userOption' . $userOptionID;
        $sql = "SELECT  userID, " . $birthday . "
                FROM    wcf1_user_option_value
                WHERE   " . $birthday . " LIKE ?";
        $statement = WCF::getDB()->prepare($sql);
        $statement->execute(['%-' . ($this->month < 10 ? '0' : '') . $this->month . '-%']);
        while ($row = $statement->fetchArray()) {
            [, , $day] = \explode('-', $row[$birthday]);
            if (!isset($data[$day])) {
                $data[$day] = [];
            }
            $data[\intval($day)][] = $row['userID'];
        }

        return $data;
    }
}
