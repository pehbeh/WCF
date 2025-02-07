<?php

namespace wcf\system\endpoint\controller\core\packages\updates\servers;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\package\update\server\PackageUpdateServer;
use wcf\data\package\update\server\PackageUpdateServerAction;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for disabling package update servers.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest('/core/packages/updates/servers/{id}/disable')]
final class DisableServer implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $server = Helper::fetchObjectFromRequestParameter($variables['id'], PackageUpdateServer::class);

        $this->assertServerCanBeDisabled($server);

        (new PackageUpdateServerAction([$server], 'toggle'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertServerCanBeDisabled(PackageUpdateServer $server): void
    {
        WCF::getSession()->checkPermissions(['admin.configuration.package.canEditServer']);

        if (!$server->canDisable()) {
            throw new PermissionDeniedException();
        }
        if ($server->isDisabled) {
            throw new PermissionDeniedException();
        }
    }
}
