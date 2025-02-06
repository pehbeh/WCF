<?php

namespace wcf\system\endpoint\controller\core\users;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use wcf\data\user\UserAction;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for enabling users.
 *
 * @author      Olaf Braun
 * @copyright   2001-2025 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @since       6.2
 */
#[PostRequest("/core/users/{id:\d+}/enable")]
final class EnableUser implements IController
{
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $user = UserProfileRuntimeCache::getInstance()->getObject($variables['id']);

        $this->assertUserCanBeEnabled($user);

        (new UserAction([$user->getDecoratedObject()], 'enable'))->executeAction();

        return new JsonResponse([]);
    }

    private function assertUserCanBeEnabled(?UserProfile $user): void
    {
        if ($user === null) {
            throw new IllegalLinkException();
        }

        if (!$user->canEnable()) {
            throw new PermissionDeniedException();
        }

        if (!$user->pendingActivation()) {
            throw new PermissionDeniedException();
        }
    }
}
