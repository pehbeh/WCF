<?php

namespace wcf\system\endpoint\controller\core\trophies;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\trophy\Trophy;
use wcf\data\trophy\TrophyAction;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for disabling trophies.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest("/core/trophies/{id:\d+}/disable")]
final class DisableTrophy implements IController
{
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $trophy = Helper::fetchObjectFromRequestParameter($variables['id'], Trophy::class);

        $this->assertTrophyCanBeDisabled($trophy);

        (new TrophyAction([$trophy], 'toggle'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertTrophyCanBeDisabled(Trophy $trophy): void
    {
        if (!\MODULE_TROPHY) {
            throw new IllegalLinkException();
        }

        WCF::getSession()->checkPermissions(['admin.trophy.canManageTrophy']);

        if ($trophy->isDisabled) {
            throw new PermissionDeniedException();
        }
    }
}
