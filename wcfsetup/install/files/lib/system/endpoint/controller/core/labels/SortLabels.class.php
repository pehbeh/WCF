<?php

namespace wcf\system\endpoint\controller\core\labels;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for saving the sorting of labels.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest('/core/labels/sort')]
final class SortLabels implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $this->assertLabelsCanBeSorted();

        $parameters = Helper::mapApiParameters($request, SortLabelsParameters::class);

        $sql = "UPDATE  wcf1_label
                SET     showOrder = ?
                WHERE   labelID = ?";
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

    private function assertLabelsCanBeSorted(): void
    {
        if (!WCF::getSession()->getPermission("admin.content.label.canManageLabel")) {
            throw new PermissionDeniedException();
        }
    }
}

/** @internal */
final class SortLabelsParameters
{
    public function __construct(
        /** @var positive-int[] */
        public readonly array $objectIDs,
    ) {
    }
}
