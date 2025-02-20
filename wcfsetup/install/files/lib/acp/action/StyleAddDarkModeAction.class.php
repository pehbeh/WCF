<?php

namespace wcf\acp\action;

use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\TextResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use wcf\data\style\Style;
use wcf\http\Helper;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\style\command\AddDarkMode;
use wcf\system\WCF;

/**
 * Adds the dark color scheme to a style.
 *
 * @author Alexander Ebert
 * @copyright 2001-2022 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.0
 * @deprecated 6.2 Use `wcf\system\endpoint\controller\core\styles\AddDarkMode` instead.
 */
final class StyleAddDarkModeAction implements RequestHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parameters = Helper::mapQueryParameters(
            $request->getQueryParams(),
            <<<'EOT'
                array {
                    id: positive-int
                }
                EOT,
        );

        $style = new Style($parameters['id']);
        if (!$style->styleID || !$style->isTainted) {
            throw new IllegalLinkException();
        }

        if (!WCF::getSession()->getPermission('admin.style.canManageStyle')) {
            throw new PermissionDeniedException();
        }

        if ($request->getMethod() === 'GET') {
            return new TextResponse('Unsupported', 400);
        } elseif ($request->getMethod() === 'POST') {
            $command = new AddDarkMode($style);
            $command();

            return new EmptyResponse();
        } else {
            throw new \LogicException('Unreachable');
        }
    }
}
