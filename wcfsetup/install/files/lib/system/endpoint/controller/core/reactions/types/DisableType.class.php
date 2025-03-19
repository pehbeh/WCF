<?php

namespace wcf\system\endpoint\controller\core\reactions\types;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\reaction\type\ReactionType;
use wcf\data\reaction\type\ReactionTypeAction;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for setting a reaction type as not assignable.
 *
 * @author Olaf Braun
 * @copyright 2001-2025 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since 6.2
 */
#[PostRequest("/core/reactions/types/{id:\d+}/disable")]
final class DisableType implements IController
{
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $reactionType = Helper::fetchObjectFromRequestParameter($variables['id'], ReactionType::class);

        $this->assertReactionTypeCanBeDisabled($reactionType);

        (new ReactionTypeAction([$reactionType], 'toggle'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertReactionTypeCanBeDisabled(ReactionType $reactionType): void
    {
        if (!\MODULE_LIKE) {
            throw new IllegalLinkException();
        }

        WCF::getSession()->checkPermissions(["admin.content.reaction.canManageReactionType"]);

        if (!$reactionType->isAssignable) {
            throw new PermissionDeniedException();
        }
    }
}
