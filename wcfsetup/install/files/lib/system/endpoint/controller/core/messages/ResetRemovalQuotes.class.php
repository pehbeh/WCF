<?php

namespace wcf\system\endpoint\controller\core\messages;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\message\quote\MessageQuoteManager;

/**
 * Resets the session variable that stores the information which quotes should be deleted at the next request.
 *
 * @author    Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since     6.2
 */
#[PostRequest('/core/messages/reset-removal-quotes')]
final class ResetRemovalQuotes implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        MessageQuoteManager::getInstance()->reset();

        return new JsonResponse([]);
    }
}
