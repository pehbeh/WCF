<?php

namespace wcf\system\endpoint\controller\core\pages;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\page\Page;
use wcf\data\page\PageAction;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for disabling pages.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest('/core/pages/{id:\d+}/disable')]
final class DisablePage implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $page = Helper::fetchObjectFromRequestParameter($variables['id'], Page::class);

        $this->assertPageCanBeDisabled($page);

        (new PageAction([$page], 'toggle'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertPageCanBeDisabled(Page $page): void
    {
        WCF::getSession()->checkPermissions(['admin.content.cms.canManagePage']);

        if (!$page->canDisable()) {
            throw new PermissionDeniedException();
        }

        if ($page->isDisabled) {
            throw new PermissionDeniedException();
        }
    }
}
