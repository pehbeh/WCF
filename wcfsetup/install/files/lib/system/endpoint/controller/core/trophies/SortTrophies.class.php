<?php

namespace wcf\system\endpoint\controller\core\trophies;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for saving the sorting of trophies.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest('/core/trophies/sort')]
final class SortTrophies implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $this->assertTrophiesCanBeSorted();

        $parameters = Helper::mapApiParameters($request, SortTrophiesParameters::class);

        $sql = "UPDATE  wcf1_trophy
                SET     showOrder = ?
                WHERE   trophyID = ?";
        $statement = WCF::getDB()->prepare($sql);

        WCF::getDB()->beginTransaction();
        foreach ($parameters->objectIDs as $showOrder => $trophyID) {
            $statement->execute([
                $showOrder,
                $trophyID,
            ]);
        }
        WCF::getDB()->commitTransaction();

        return new JsonResponse([]);
    }

    private function assertTrophiesCanBeSorted(): void
    {
        if (!\MODULE_TROPHY) {
            throw new IllegalLinkException();
        }

        if (!WCF::getSession()->getPermission("admin.trophy.canManageTrophy")) {
            throw new PermissionDeniedException();
        }
    }
}

/** @internal */
final class SortTrophiesParameters
{
    public function __construct(
        /** @var positive-int[] */
        public readonly array $objectIDs,
    ) {
    }
}
